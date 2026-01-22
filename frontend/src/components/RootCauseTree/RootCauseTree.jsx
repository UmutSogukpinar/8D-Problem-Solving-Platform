import { useMemo } from "react";
import NodeCard from "./NodeCard";
import { buildTree } from "../../utils/buildTree";
import { apiFetch } from "../../api/client";
import { useUser } from "../../context/UserContext";

export default function RootCauseTree({ problemId, nodes, onReload })
{
    const { user, loading } = useUser();

    const tree = useMemo(
        () => buildTree(nodes || []),
        [nodes]
    );

    const handleAddChild = async (parentId, description) =>
    {
        if (loading || !user)
        {
            throw new Error("User not loaded");
        }

        await apiFetch("/8d/rootcauses", {
            method: "POST",
            body: JSON.stringify({
                problem_id: Number(problemId),
                parent_id: parentId,
                author_id: user.userId,
                description,
            }),
        });

        onReload();
    };

    if (loading)
    {
        return (<div>Loading user...</div>);
    }

    return (
        <div>
            {tree.map((root) => (
                <NodeCard
                    key={root.id}
                    node={root}
                    depth={0}
                    onAddChild={handleAddChild}
                />
            ))}
        </div>
    );
}
