export interface ResponseWithErrorField
{
    error: string;
}

export async function requestPOST<T extends ResponseWithErrorField>(url: string, body: object): Promise<T | null>
{
    try
    {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(body)
        })
        if(!response.ok)
            throw new Error("Server error");
        const data = await response.json() as T;

        console.log(data);

        return data;
    }
    catch(err)
    {
        return null;
    }
}
export async function requestGET<T extends ResponseWithErrorField>(url: string): Promise<T | null>
{
    try
    {
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            },
        })
        if(!response.ok)
            throw new Error("Server error");
        const data = await response.json() as T;

        console.log(data);

        return data;
    }
    catch(err)
    {
        return null;
    }
}