import { useMemo, useRef, useState } from "react";
import { IxPushCard } from "@siemens/ix-react";
import { iconBulb } from "@siemens/ix-icons/icons";

export default function NodeCard({ node, depth, onAddChild })
{
    const [open, setOpen] = useState(true);
    const [newChild, setNewChild] = useState("");
    const textareaRef = useRef(null);

    const hasChildren = useMemo(
        () => Boolean(node.children && node.children.length > 0),
        [node.children]
    );

    const subheading = useMemo(
        () =>
            [
                node.createdAt && `Created: ${node.createdAt}`,
                node.authorName && `Author: ${node.authorName}`,
            ].filter(Boolean).join(" · "),
        [node.createdAt, node.authorName]
    );

    const toggle = () =>
    {
        if (!hasChildren)
        {
            return;
        }
        setOpen((v) => !v);
    };

    const resizeTextarea = () =>
    {
        const el = textareaRef.current;
        if (!el)
        {
            return;
        }

        el.style.height = "auto";
        el.style.height = `${el.scrollHeight}px`;
    };

    const submitChild = () =>
    {
        const value = newChild.trim();

        if (!value)
        {
            return;
        }

        if (onAddChild)
        {
            onAddChild(node.id, value);
        }

        setNewChild("");
        setOpen(true);

        requestAnimationFrame(resizeTextarea);
    };

    const onTextareaKeyDown = (e) =>
    {
        if (e.key === "Enter" && !e.shiftKey)
        {
            e.preventDefault();
            submitChild();
        }
    };

    const onTextareaChange = (e) =>
    {
        setNewChild(e.target.value);
        resizeTextarea();
    };

    return (
        <div style={{ marginLeft: depth * 24 }}>
            <IxPushCard
                icon={iconBulb}
                heading={node.description}
                subheading={subheading}
                variant="outline"
                onClick={toggle}
                style={{
                    marginBottom: 10,
                    cursor: hasChildren ? "pointer" : "default",
                }}
            >
                <div style={{ display: "flex", flexDirection: "column", gap: 10 }}>

                    <div
                        onClick={(e) => e.stopPropagation()}
                        style={{
                            display: "flex",
                            gap: 10,
                            alignItems: "flex-start",
                            width: "100%",
                        }}
                    >
                        <textarea
                            ref={textareaRef}
                            value={newChild}
                            placeholder="Add child cause..."
                            rows={1}
                            onChange={onTextareaChange}
                            onKeyDown={onTextareaKeyDown}
                            style={{
                                flex: 1,
                                width: "100%",
                                minHeight: 38,
                                resize: "none",
                                padding: "8px 12px",
                                fontSize: 13,
                                borderRadius: 10,
                                border: "1px solid rgba(255,255,255,0.25)",
                                background: "transparent",
                                color: "inherit",
                                outline: "none",
                                lineHeight: "1.4",
                                overflow: "hidden",
                            }}
                        />

                        <button
                            type="button"
                            onClick={(e) =>
                            {
                                e.stopPropagation();
                                submitChild();
                            }}
                            style={{
                                padding: "8px 12px",
                                fontSize: 12,
                                borderRadius: 10,
                                cursor: "pointer",
                                whiteSpace: "nowrap",
                            }}
                        >
                            Add
                        </button>
                    </div>
                </div>
            </IxPushCard>

            {hasChildren && open && (
                <div>
                    {node.children.map((child) => (
                        <NodeCard
                            key={child.id}
                            node={child}
                            depth={depth + 1}
                            onAddChild={onAddChild}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
