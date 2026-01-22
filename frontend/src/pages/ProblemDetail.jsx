import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import RootCauseTree from "../components/RootCauseTree/RootCauseTree";
import { apiFetch } from "../api/client";

export default function ProblemDetail()
{
    const { id } = useParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() =>
    {
        setLoading(true);
        setError(null);

        apiFetch(`/8d/rootcauses/${id}/tree`)
            .then((res) =>
            {
                setData(res);
                setLoading(false);
            })
            .catch((e) =>
            {
                setError(e?.message || "Failed to load problem");
                setLoading(false);
            });
    }, [id]);

    if (loading)
    {
        return (<div>Loading...</div>);
    }

    if (error)
    {
        return (<div>{error}</div>);
    }

    const problem = data?.problem ?? data?.problemDetails ?? data ?? null;
    const nodes = data?.tree ?? data?.nodes ?? [];
    return (
        <div>
            <h2>{problem?.title ?? "Untitled"}</h2>
            <p>{problem?.description ?? ""}</p>

            <RootCauseTree nodes={nodes} />
        </div>
    );
}
