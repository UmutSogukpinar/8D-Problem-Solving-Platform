import { useEffect, useState } from "react";
import {useUser} from "../context/UserContext"
import Form from "../components/Form";
import Spinner from "../components/Spinner";
import { apiFetch } from "../api/client";

const ProblemInsert = () =>
{
	const [crews, setCrews] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);

	const { user } = useUser();
	
	useEffect(() =>
	{
		async function fetchCrews()
		{
			try
			{
				setLoading(true);
				setError(null);

				const data = await apiFetch("/8d/crew");

				setCrews(data);
			}
			catch (e)
			{
				setError(e.message ?? "Failed to load crews");
			}
			finally
			{
				setLoading(false);
			}
		}

		fetchCrews();
	}, []);

	if (loading)
	{
		return (<Spinner />);
	}

	if (error)
	{
		return (<div style={{ color: "red" }}>{error}</div>);
	}

	return (<Form crews={crews} user={user} />);
};

export default ProblemInsert;
