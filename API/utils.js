
export async function extraireEtFormaterJSON(texte) {
    const pattern = /\{(.+)\}/s;
    const match = texte.match(pattern);

    if (match) {
        const jsonString = match[0];
        try {
            const jsonData = JSON.parse(jsonString);
            return jsonData;
        } catch (error) {
            console.log(error);
            return null;
        }
    } else {
        console.log('No JSON found');
        return null;
    }
}
