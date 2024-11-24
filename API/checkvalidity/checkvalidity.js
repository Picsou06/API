export async function checkNbSerieValidity(exercices) {
    if (!Array.isArray(exercices)) {
        return false;
    }

    for (const item of exercices) {
        if (item.nombre_de_series !== item.poids_par_serie.length) {
            return false;
        }
    }

    return true;
}

export async function checkTypeValidity(exercices) {
    if (!Array.isArray(exercices)) {
        return false;
    }

    for (const item of exercices) {
        if (item.nombre_de_series !== item.poids_par_serie.length) {
            return false;
        }
        if (item.type !== "Cardio" && item.type !== "Musculation") {
            return false;
        }
    }

    return true;
}

export async function checkJourValidity(seances) {
    const validDays = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];

    if (!Array.isArray(seances)) {
        return false;
    }

    for (const item of seances) {
        if (!validDays.includes(item.jour)) {
            return false;
        }
    }

    return true;
}

export async function checkDureeExerciceValidity(seances) {
    if (!Array.isArray(seances)) {
        return false;
    }

    for (const item of seances) {
        let totalDureeExercices = 0;
        for (const exercice of item.exercices) {
            totalDureeExercices += parseInt(exercice.duree_exercice);
        }

        if (totalDureeExercices > item.duree_seance_minutes) {
            return false;
        }
    }

    return true;
}

export async function checkPoidsValidity(exercices) {
    if (!Array.isArray(exercices)) {
        return false;
    }

    for (const item of exercices) {
        for (const poids of item.poids_par_serie) {
            if (poids < 0) {
                return false;
            }
        }
    }

    return true;
}

export async function checkAllValidity(data) {
    const semaines = data.seance_de_sport.semaines;

    for (const semaine of semaines) {
        for (const seance of semaine.seances) {
            const exercices = seance.exercices;
            if (!(await checkNbSerieValidity(exercices)) ||
                !(await checkTypeValidity(exercices)) ||
                !(await checkJourValidity(semaine.seances)) ||
                !(await checkDureeExerciceValidity(semaine.seances)) ||
                !(await checkPoidsValidity(exercices))) {
                return false;
            }
        }
    }

    return true;
}