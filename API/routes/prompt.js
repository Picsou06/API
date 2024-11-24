import express from 'express';
import { verifyToken } from '../middleware/auth.js';
import { query } from '../database/db.js';
import { getGroqChatCompletion } from './groq.js';
import { extraireEtFormaterJSON } from '../utils.js';
import { checkAllValidity } from '../checkvalidity/checkvalidity.js';

const router = express.Router();

router.get('/getprompt', async (req, res) => {
  const token = req.headers.authorization?.split(' ')[1];
  const { objectif, niveau, exercices } = req.query;

  if (!token || !objectif || !niveau || !exercices) {
    return res.status(400).json({ error: 'Missing parameters' });
  }

  const user = await verifyToken(token);
  if (!user) {
    return res.status(401).json({ error: 'Invalid token' });
  }

  const userInfo = await query('SELECT * FROM users WHERE id = ?', [user.id]);
  if (userInfo.length === 0) {
    return res.status(404).json({ error: 'User information not found' });
  }

  const birthDate = new Date(userInfo[0].naissance);
  const ageDifMs = Date.now() - birthDate.getTime();
  const ageDate = new Date(ageDifMs);
  const age = Math.abs(ageDate.getUTCFullYear() - 1970);  
  let i = 1;
  let finalAnswer;
  let oldprompt;
  let chatCompletion;
  let answer;
  let prompt;
  let returnedAnswer = null;
  while (!returnedAnswer) {
    finalAnswer = `{
      "seance_de_sport": {
        "informations_de_base": {
          "nombre_de_seances_par_semaine": 4,
          "duree_maximale_seance_minutes": ${userInfo[0].duree_seance_max}
        },
        "semaines": [`;
    oldprompt = `Aidez-moi à créer un programme d'entraînement adapté à mon niveau ${niveau} et à mes objectifs ${objectif}. Proposez-moi des exercices que je peux faire avec ${exercices} pour atteindre mes objectifs. Mes informations : ma taille : ${userInfo[0].taille}, mon poids : ${userInfo[0].poids}, mon âge : ${age}, mon sexe : ${userInfo[0].sexe}, mon poids maximum porté : ${userInfo[0].max_weight}, nombre maximum de séances par semaine : ${userInfo[0].nb_seance_max}, durée maximum de séance de : ${userInfo[0].duree_seance_max}. Je veux que ta réponse soit uniquement sous la forme d'un JSON structuré comme suit (et sans autre commentaire ni code) :

    Faites attention à bien organiser le programme d'entraînement sur plusieurs semaines, si nécessaire. Vous devez limiter le nombre de séances à 4 maximum par semaine. Chaque semaine peut inclure des exercices différents, avec des variations sur les poids, les séries et les types d'exercices. Assurez-vous de respecter les informations suivantes pour chaque jour et chaque séance :
    - Ne proposez que des exercices adaptés à mon sexe, poids maximum porté, et ma condition physique.
    - Respectez la durée de la séance donnée : la durée de chaque séance ne doit pas dépasser ${userInfo[0].duree_seance_max} minutes.
    - Crée moi uniquement la structure de la première semaine.
    
    {
            "numero_semaine": 1,
            "seances": [
              {
                "jour": "Le jour de la semaine",
                "duree_seance_minutes": Durée de la première seance (doit respecter la durée maximale) Doit être un int, ne pas afficher les jours de repos!
                "exercices": [
                  {
                    "nom": "Nom de l'exercice",
                    "description": "Description de l'exercice, explication du mouvement à réaliser",
                    "nombre_de_series": "Nombre de séries pour cet exercice",
                    "poids_par_serie": [Liste des poids pour chaque serie. Il n'ont pas besoin d'être toujours les même par exemple tu peux monter le poids sur chaque série ou autre. Toujours mettre le même nombre de poids que de serie. Si pas de poids pour un exercice mettre 0],
                    "duree_exercice": "Durée de l'exercice en minutes, -1 si il n'y a pas de durée",
                    "repos_entre_series": "Repos entre chaque série",
                    "type": "Musculation, Cardio..."
                  }
                  // Répétez la structure pour chaque exercice de la séance
                ]
              }
              // Répétez la structure pour chaque séance de la semaine
            ]
          },
    `;
    chatCompletion = await getGroqChatCompletion(oldprompt);
    answer = await extraireEtFormaterJSON(chatCompletion.choices[0].message.content);
    while (!answer) {
      console.log("Answer is null")
      chatCompletion = await getGroqChatCompletion(oldprompt);
      answer = await extraireEtFormaterJSON(chatCompletion.choices[0].message.content);
    }
  for (i = 2; i <= 4; i++) {
      if (i === 2) {
      chatCompletion = await getGroqChatCompletion(oldprompt);
      answer = await extraireEtFormaterJSON(chatCompletion.choices[0].message.content);

      while (!answer) {
        console.log("Answer is null")
        chatCompletion = await getGroqChatCompletion(prompt);
        answer = await extraireEtFormaterJSON(chatCompletion.choices[0].message.content);
      }
      finalAnswer += JSON.stringify(answer);
    }
    else {
      finalAnswer += ','+JSON.stringify(answer);
    }

      prompt = `Aidez-moi à créer un programme d'entraînement adapté à mon niveau ${niveau} et à mes objectifs ${objectif}. Proposez-moi des exercices que je peux faire avec ${exercices} pour atteindre mes objectifs. Mes informations : ma taille : ${userInfo[0].taille}, mon poids : ${userInfo[0].poids}, mon âge : ${age}, mon sexe : ${userInfo[0].sexe}, mon poids maximum porté : ${userInfo[0].max_weight}, nombre maximum de séances par semaine : ${userInfo[0].nb_seance_max}, durée maximum de séance de : ${userInfo[0].duree_seance_max}:

      Faites attention à bien organiser le programme d'entraînement sur plusieurs semaines, si nécessaire. Vous devez limiter le nombre de séances à 4 maximum par semaine. Chaque semaine peut inclure des exercices différents, avec des variations sur les poids, les séries et les types d'exercices. Assurez-vous de respecter les informations suivantes pour chaque jour et chaque séance :
      - Ne proposez que des exercices adaptés à mon sexe, poids maximum porté, et ma condition physique.
      - Respectez la durée de la séance donnée : la durée de chaque séance ne doit pas dépasser ${userInfo[0].duree_seance_max} minutes.
      - Crée moi uniquement la structure de la semaine ${i} et met le "numero_semaine": à ${i}. Attention si le numero de semaine est le même que la semaine précédente, cela est une erreur.
      - Sachant que la semaine dernière j'ai fais ces exercices ${answer}.

      Je veux que ta réponse soit uniquement sous la forme d'un JSON structuré comme suit (et sans autre commentaire ni code) :
      {
              "numero_semaine": ${i},
              "seances": [
                {
                  "jour": "Le jour de la semaine",
                  "duree_seance_minutes": "Durée de la première seance (doit respecter la durée maximale)",
                  "exercices": [
                    {
                      "nom": "Nom de l'exercice",
                      "description": "Description de l'exercice, explication du mouvement à réaliser",
                      "nombre_de_series": "Nombre de séries pour cet exercice",
                      "poids_par_serie": [Liste des poids pour chaque serie. Il n'ont pas besoin d'être toujours les même par exemple tu peux monter le poids sur chaque série ou autre. Toujours mettre le même nombre de poids que de serie. Si pas de poids pour un exercice mettre 0 il doit toujours être un tableau],
                      "duree_exercice": "Durée de l'exercice en minutes, 0 si il n'y a pas de durée",
                      "repos_entre_series": "Repos entre chaque série, 0 si il n'y a pas de repos",
                      "type": "Musculation, Cardio..."
                    }
                    // Répétez la structure pour chaque exercice de la séance
                  ]
                }
                // Répétez la structure pour chaque séance de la semaine
              ]
            },
      `;
    }
    finalAnswer += ','+JSON.stringify(answer);
    finalAnswer += `]
        }
      }`;
    console.log("FinalAnswer: " + finalAnswer);
    returnedAnswer = await extraireEtFormaterJSON(finalAnswer);
    if (!checkAllValidity(returnedAnswer)) {
      returnedAnswer = null;
    }
  }
  res.json(returnedAnswer);
});

export default router;