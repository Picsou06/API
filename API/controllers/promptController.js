import { getGroqChatCompletion } from '../server.js';

export async function getPrompt(req, res) {
  const { objectif, niveau, exercices } = req.query;

  if (!objectif || !niveau || !exercices) {
    return res.status(400).json({ error: 'Missing required parameters' });
  }

  const user = req.user;
  const sexe = user.sexe === 0 ? 'Male' : user.sexe === 1 ? 'Femme' : 'Autre';
  const informations = `Taille: ${user.taille}, Poids: ${user.poids}, Age: ${user.age}, Sexe: ${sexe}, durée maximum des seances: ${user.duree_seance_max}, nombre de seances par semaine: ${user.nb_seance_max}`;

  const prompt = `Aidez-moi à créer un programme d'entraînement adapté à mon niveau ${niveau} et à mes objectifs ${objectif}. Proposez-moi des exercices que je peux faire avec ${exercices} pour atteindre mes objectifs. Mes informations: ${informations}. Ecris moi la reponse sous un format json avec au debut les informations du nombre de seances par semaine puis affiche moi les informations pour chaque seance nom de l'exercice, nombre de serie. Nombre de repetition par serie, poids par serie, temps de repos par serie. L'objectif et de trier les informations pour une application de sport donc mets toutes les informations possible, pas de choix.`;

  try {
    const chatCompletion = await getGroqChatCompletion(prompt);
    res.json(chatCompletion);
  } catch (error) {
    res.status(500).json({ error: 'Failed to get prompt completion' });
  }
}