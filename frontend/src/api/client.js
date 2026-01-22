const BASE_URL = "http://localhost";
const MOCK_USER_ID = 1;

export async function apiFetch(path, options = {})
{
    const headers = new Headers(options.headers || {});
    headers.set("Content-Type", "application/json");
    headers.set("X-Mock-User-Id", String(MOCK_USER_ID));

    const res = await fetch(`${BASE_URL}${path}`, {
        ...options,
        headers,
    });

    const contentType = res.headers.get("content-type") || "";
    const isJson = contentType.includes("application/json");

    let payload = null;

    if (isJson)
    {
        try
        {
            payload = await res.json();
        }
        catch
        {
            payload = null;
        }
    }
    else
    {
        try
        {
            payload = await res.text();
        }
        catch
        {
            payload = null;
        }
    }

    if (!res.ok)
    {
        const message =
            payload && typeof payload === "object" && payload.message
                ? payload.message
                : (typeof payload === "string" && payload.trim() ? payload : `HTTP ${res.status}`);

        const err = new Error(message);
        err.status = res.status;
        err.payload = payload;
        throw err;
    }

    return (payload);
}
