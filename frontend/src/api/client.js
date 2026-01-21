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

	if (!res.ok)
	{
		throw new Error(`HTTP ${res.status}`);
	}

	return (await res.json());
}
