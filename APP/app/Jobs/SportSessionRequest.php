<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\sportSession;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User_information;

class SportSessionRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $goal;
    protected $level;
    protected $machines;
    protected $duration;
    protected $token;

    /**
     * Create a new job instance.
     */
    public function __construct($goal, $level, $machines, $duration, $token)
    {
        $this->goal = $goal;
        $this->level = $level;
        $this->machines = $machines;
        $this->duration = $duration;
        $this->token = $token;
    }

    public function handle(): void
    {
        // Valider le token et récupérer l'utilisateur
        $user_id = DB::table('sessions')->where('id', $this->token)->value('user_id');
        Log::info('User ID: ' . $user_id);
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            Log::error('Token invalide.');
            return;
        }

        $userInfo = User_information::where('user_id', $user_id)->first();
        if (!$userInfo) {
            Log::error('Informations utilisateur introuvables.');
            return;
        }

        $birthDate = new \DateTime($user->birth);
        $currentDate = new \DateTime();
        $age = $currentDate->diff($birthDate)->y;

        // Générer les prompts pour créer les programmes
        $programs = [];
        for ($week = 1; $week <= $this->duration; $week++) {
            $prompt = $this->generatePrompt($week, $userInfo, $age);
            $program = $this->getGroqChatCompletion($prompt);

            if ($program) {
                $programs[] = $program;
            }
        }

        // Sauvegarder les programmes générés dans la base de données
        foreach ($programs as $weekProgram) {
            foreach ($weekProgram['seances'] as $seance) {
                $this->saveSportSession($seance, $weekProgram['numero']);
            }
        }
    }

    private function generatePrompt($week, $userInfo, $age): string
    {
        return "
            Aidez-moi à créer un programme d'entraînement adapté à mon niveau {$this->level} et à mes objectifs {$this->goal}.
            Proposez des exercices que je peux réaliser avec {$this->machines}.
            Mes informations : 
            - Taille : {$userInfo->size}
            - Poids : {$userInfo->poids}
            - Âge : {$age}
            - Sexe : {$userInfo->sexe}
            - Poids maximum porté : {$userInfo->max_weight}
            - Séances par semaine : {$userInfo->nb_session}
            - Durée maximale de séance : {$userInfo->session_duration} minutes.

            Limitez le programme à 4 séances maximum par semaine. Organisez chaque semaine différemment avec des variations.
            Structurez uniquement la semaine {$week} sous forme JSON :
            {
                \"numero_semaine\": {$week},
                \"seances\": [
                    {
                        \"jour\": \"Jour de la semaine\",
                        \"duree_seance_minutes\": 0,
                        \"machines\": [
                            {
                                \"nom\": \"Nom de l'exercice\",
                                \"description\": \"Description\",
                                \"nombre_de_series\": 0,
                                \"poids_par_serie\": [0],
                                \"duree_exercice\": 0,
                                \"repos_entre_series\": 0,
                                \"type\": \"Type d'exercice\"
                            }
                        ]
                    }
                ]
            }";
    }

    private function getGroqChatCompletion(string $prompt)
    {
        try {
            // Configuration de l'API Groq
            $apiKey = env('ACCESS_GROK_TOKEN_SECRET');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'model' => 'gemma2-9b-it',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Extraction et validation de la réponse JSON avec la fonction extraireEtFormaterJSON
                $formattedResponse = $this->extraireEtFormaterJSON($data['choices'][0]['message']['content']);
                
                if ($formattedResponse) {
                    return $formattedResponse;
                } else {
                    Log::error('Réponse JSON invalide : format incorrect');
                    return null;
                }
            } else {
                Log::error('Erreur API Groq : ' . $response->status() . ' - ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'appel à l\'API Groq : ' . $e->getMessage());
            return null;
        }
    }

    // Fonction pour extraire et formater le JSON
    private function extraireEtFormaterJSON($texte)
    {
        Log::info('Réponse JSON brute : ' . $texte);
        $pattern = '/\{(.+)\}/s';
        if (preg_match($pattern, $texte, $match)) {
            $jsonString = $match[0];
            try {
                $jsonData = json_decode($jsonString, true);
                return $jsonData;
            } catch (\Exception $error) {
                Log::error('Erreur lors de l\'analyse JSON : ' . $error->getMessage());
                return null;
            }
        } else {
            Log::error('Aucun JSON trouvé dans la réponse');
            return null;
        }
    }


    private function saveSportSession($seance, $weekNumber)
    {
        $date = new \DateTime();
        $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $currentDayOfWeek = $date->format('N') - 1; // 0 (Lundi) à 6 (Dimanche)
        $targetDayOfWeek = array_search($seance['jour'], $daysOfWeek);

        if ($targetDayOfWeek !== false) {
            $daysToAdd = ($targetDayOfWeek - $currentDayOfWeek + 7) % 7;
            if ($daysToAdd == 0) {
                $daysToAdd = 7;
            }
            $date->modify("+$daysToAdd days");
        }

        if ($weekNumber > 1) {
            $date->modify('+' . (7 * ($weekNumber - 1)) . ' days');
        }

        sportSession::create([
            'date' => $date->format('Y-m-d'),
            'duration' => $seance['duree_seance_minutes'],
            'details' => json_encode($seance['machines']),
        ]);

        Log::info('Sport session created for date: ' . $date->format('Y-m-d'));
    }
}
