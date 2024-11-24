<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\sportSession;

class SportSessionRequest implements ShouldQueue
{
    use Queueable;

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
        $url = 'http://Picsou06.fr:3002/api/getprompt';
        
        // Préparation des paramètres de requête
        $queryParams = http_build_query([
            'goal' => $this->goal,
            'level' => $this->level,
            'machines' => $this->machines,
            'duration' => $this->duration,
            'token' => $this->token,
        ]);

        $fullUrl = $url . '?' . $queryParams;

        // Initialisation de cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Ajout du header d'authentification
        $headers = [
            'Authorization:Bearer ' . $this->token,
        ];
        print_r($headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Exécution de la requête
        $response = curl_exec($ch);

        // Gestion des erreurs
        if (curl_errno($ch)) {
            echo 'Erreur cURL : ' . curl_error($ch);
        }

        // Fermeture de cURL
        curl_close($ch);

        // Traitement de la réponse
        $data = json_decode($response, true);

        // Création des sessions de sport
        foreach ($data['seance_de_sport']['semaines'] as $semaine) {
            foreach ($semaine['seances'] as $seance) {
                $sportSession = new sportSession();
                $date = new \DateTime();
                $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                $currentDayOfWeek = $date->format('N') - 1; // 0 (for Monday) through 6 (for Sunday)
                $targetDayOfWeek = array_search($seance['jour'], $daysOfWeek);

                if ($targetDayOfWeek !== false) {
                    $daysToAdd = ($targetDayOfWeek - $currentDayOfWeek + 7) % 7;
                    if ($daysToAdd == 0) {
                        $daysToAdd = 7;
                    }
                    $date->modify("+$daysToAdd days");
                }

                // If it's the second week, add an additional 7 days
                if ($semaine['numero'] == 2) {
                    $date->modify('+7 days');
                }

                $sportSession->date = $date->format('Y-m-d');
                $sportSession->duration = $seance['duree_seance_minutes'];
                $sportSession->details = json_encode($seance['machines']);
                $sportSession->save();
                \Log::info('Sport session created for date: ' . $sportSession->date);
            }
        }
    }
}