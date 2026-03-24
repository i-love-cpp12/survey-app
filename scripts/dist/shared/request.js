export async function requestPOST(url, body) {
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(body)
        });
        if (!response.ok)
            throw new Error("Server error");
        const data = await response.json();
        console.log(data);
        return data;
    }
    catch (err) {
        return null;
    }
}
export async function requestGET(url) {
    try {
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            },
        });
        if (!response.ok)
            throw new Error("Server error");
        const data = await response.json();
        console.log(data);
        return data;
    }
    catch (err) {
        return null;
    }
}
