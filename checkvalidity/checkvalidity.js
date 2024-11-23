export async function checkNbSerieValidity(data) {
    if (!Array.isArray(data)) {
        throw new Error("Input should be an array of objects");
    }

    data.forEach(item => {
        if (item.nombre_de_series !== item.poids_par_serie.length) {
            throw new Error(`Mismatch in series count for item: ${JSON.stringify(item)}`);
        }
    });

    return true;
}

export async function checkTypeValidity(data) {
    if (!Array.isArray(data)) {
        throw new Error("Input should be an array of objects");
    }

    data.forEach(item => {
        if (item.nombre_de_series !== item.poids_par_serie.length) {
            throw new Error(`Mismatch in series count for item: ${JSON.stringify(item)}`);
        }
        if (item.type !== "Cardio" && item.type !== "Musculation") {
            throw new Error(`Invalid type for item: ${JSON.stringify(item)}`);
        }
    });

    return true;
}

export async function checkJourValidity(data) {
    const validDays = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];

    if (!Array.isArray(data)) {
        throw new Error("Input should be an array of objects");
    }

    data.forEach(item => {
        if (!validDays.includes(item.jour)) {
            throw new Error(`Invalid day for item: ${JSON.stringify(item)}`);
        }
    });

    return true;
}

export async function checkDureeExerciceValidity(data) {
    if (!Array.isArray(data)) {
        throw new Error("Input should be an array of objects");
    }

    data.forEach(item => {
        let totalDureeExercices = 0;
        item.exercices.forEach(exercice => {
            totalDureeExercices += parseInt(exercice.duree_exercice);
        });

        if (totalDureeExercices > item.duree_seance_minutes) {
            throw new Error(`Total duration of exercises exceeds session duration for item: ${JSON.stringify(item)}`);
        }
    });

    return true;
}

