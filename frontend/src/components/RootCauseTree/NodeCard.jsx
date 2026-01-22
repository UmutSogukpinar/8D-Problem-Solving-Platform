import { useMemo, useState } from "react";
import { IxPushCard } from "@siemens/ix-react";
import { iconBulb } from "@siemens/ix-icons/icons";

export default function NodeCard({ node, depth })
{
    const [open, setOpen] = useState(true);

    const hasChildren = useMemo(
        () => (node.children && node.children.length > 0),
        [node.children]
    );

    const toggle = () =>
    {
        if (!hasChildren)
        {
            return;
        }
        setOpen((v) => !v);
    };

    return (
        <div style={{ marginLeft: depth * 96 }}>
            <IxPushCard
                icon={iconBulb}
                heading={node.description}
                subheading={node.createdAt ? `Created at: ${node.createdAt}` : ""}
                variant="outline"
                onClick={toggle}
                style={{
                    marginBottom: 10,
                    cursor: hasChildren ? "pointer" : "default",
                }}
            >
                <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
                    <span style={{ fontSize: 12, opacity: 0.75 }}>
                        #{node.id}
                    </span>

                    {hasChildren && (
                        <span style={{ fontSize: 12, opacity: 0.75 }}>
                            {open ? "▾ Collapse" : "▸ Expand"} ({node.children.length})
                        </span>
                    )}
                </div>
            </IxPushCard>

            {hasChildren && open && (
                <div>
                    {node.children.map((child) => (
                        <NodeCard
                            key={child.id}
                            node={child}
                            depth={depth + 1}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
