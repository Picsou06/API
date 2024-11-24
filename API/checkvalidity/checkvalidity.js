export async function checkNbSerieValidity(machines) {
    if (!Array.isArray(machines)) {
        return false;
    }

    for (const item of machines) {
        if (item.nombre_de_series !== item.poids_par_serie.length) {
            return false;
        }
    }

    return true;
}

export async function checkTypeValidity(machines) {
    if (!Array.isArray(machines)) {
        return false;
    }

    for (const item of machines) {
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
        let totalDureemachines = 0;
        for (const exercice of item.machines) {
            totalDureemachines += parseInt(exercice.duree_exercice);
        }

        if (totalDureemachines > item.duree_seance_minutes) {
            return false;
        }
    }

    return true;
}

export async function checkPoidsValidity(machines) {
    if (!Array.isArray(machines)) {
        return false;
    }

    for (const item of machines) {
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
            const machines = seance.machines;
            if (!(await checkNbSerieValidity(machines)) ||
                !(await checkTypeValidity(machines)) ||
                !(await checkJourValidity(semaine.seances)) ||
                !(await checkDureeExerciceValidity(semaine.seances)) ||
                !(await checkPoidsValidity(machines))) {
                return false;
            }
        }
    }

    return true;
}