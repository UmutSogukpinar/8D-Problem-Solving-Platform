import { useMemo } from "react";
import NodeCard from "./NodeCard.jsx";
import { buildTree } from "../../utils/buildTree.js";

export default function RootCauseTree({ nodes })
{
    const tree = useMemo(
        () => buildTree(nodes || []),
        [nodes]
    );

    if (!nodes || nodes.length === 0)
    {
        return (
            <div style={{ opacity: 0.7 }}>
                No nodes yet.
            </div>
        );
    }

    return (
        <div>
            {tree.map((root) => (
                <NodeCard
                    key={root.id}
                    node={root}
                    depth={0}
                />
            ))}
        </div>
    );
}
