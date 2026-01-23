import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import RootCauseTree from "../components/RootCauseTree/RootCauseTree";
import { apiFetch } from "../api/client";
import { useUser } from '../context/UserContext'

export default function ProblemDetail()
{
    const { id } = useParams();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const [firstDesc, setFirstDesc] = useState("");
    const [creatingFirst, setCreatingFirst] = useState(false);
    const [createError, setCreateError] = useState(null);

    const { user } = useUser()
    

    const loadTree = async () =>
    {
        setLoading(true);
        setError(null);

        try
        {
            const res = await apiFetch(`/8d/rootcauses/${id}/tree`);
            setData(res);
        }
        catch (e)
        {
            setError(e?.message || "Failed to load problem");
        }
        finally
        {
            setLoading(false);
        }
    };

    const createFirstRoot = async (e) =>
    {
        e.preventDefault();

        const desc = firstDesc.trim();
        if (!desc)
        {
            setCreateError("Description is required.");
            return;
        }

        setCreatingFirst(true);
        setCreateError(null);

        try
        {
            await apiFetch(`/8d/rootcauses`, {
                method: "POST",
                body: JSON.stringify({
                    problem_id: Number(id),
                    parent_id: null,
                    description: desc,
                    author_id: Number(user.userId)
                }),
            });

            setFirstDesc("");
            await loadTree();
        }
        catch (err)
        {
            setCreateError(err?.message || "Failed to create root cause");
        }
        finally
        {
            setCreatingFirst(false);
        }
    };

    useEffect(() =>
    {
        if (!id)
        {
            return;
        }
        loadTree();
    }, [id]);

    if (loading) return (<div>Loading...</div>);
    if (error) return (<div>{error}</div>);

    const problem = data?.problem ?? data?.problemDetails ?? data ?? null;
    const nodes = data?.tree ?? data?.nodes ?? [];
    const hasNodes = Array.isArray(nodes) && nodes.length > 0;

    return (
        <div>
            <h2>{problem?.title ?? "Untitled"}</h2>
            <p>{problem?.description ?? ""}</p>

            {!hasNodes ? (
                <div style={{ marginTop: 16 }}>
                    <h3>Start root cause analysis</h3>
                    <p>No nodes yet. Create the first root cause to begin.</p>

                    <form onSubmit={createFirstRoot} style={{ display: "grid", gap: 8, maxWidth: 720 }}>
                        <textarea
                            value={firstDesc}
                            onChange={(e) => setFirstDesc(e.target.value)}
                            placeholder="Describe the first (root) cause..."
                            maxLength={2000}
                            disabled={creatingFirst}
                            style={{ minHeight: 140 }}
                        />

                        {createError && <div style={{ color: "crimson" }}>{createError}</div>}

                        <button type="submit" disabled={creatingFirst}>
                            {creatingFirst ? "Creating..." : "Create first root cause"}
                        </button>
                    </form>
                </div>
            ) : (
                <RootCauseTree
                    nodes={nodes}
                    problemId={id}
                    onReload={loadTree}
                />
            )}
        </div>
    );
}
