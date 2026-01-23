import { useMemo } from "react";
import { useNavigate } from "react-router-dom";
import NodeCard from "./NodeCard";
import { buildTree } from "../../utils/buildTree";
import { apiFetch } from "../../api/client";
import { useUser } from "../../context/UserContext";

export default function RootCauseTree({ problemId, nodes, onReload })
{
    const { user, loading } = useUser();
    const navigate = useNavigate();

    const tree = useMemo(
        () => buildTree(nodes || []),
        [nodes]
    );

    const handleAddChild = async (parentId, description) => {
        if (loading || !user) {
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

    const handleToggleRootCause = async (nodeId, newStatus) => {
        await apiFetch(`/8d/rootcauses/${nodeId}/is_root_cause`, {
            method: "PATCH",
            body: JSON.stringify({
                is_root_cause: newStatus,
            }),
        });

        onReload();
    };

    const handleNavigate = (nodeId) => {
        navigate(`/root-causes/${nodeId}`);
    };

    const handleNavigateSolutions = (pid) => {
        navigate(`/problems/${pid}/solutions`);
    };

    if (loading) {
        return (<div>Loading user...</div>);
    }

    return (
        <div>
            {tree.map((root) => (
                <NodeCard
                    key={root.id}
                    node={root}
                    depth={0}
                    problemId={problemId}
                    onAddChild={handleAddChild}
                    onToggleRootCause={handleToggleRootCause}
                    onNavigate={handleNavigate}
                    onNavigateSolutions={handleNavigateSolutions}
                />
            ))}
        </div>
    );
}
