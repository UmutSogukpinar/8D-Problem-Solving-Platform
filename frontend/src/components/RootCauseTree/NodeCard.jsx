import { useMemo, useRef, useState } from "react";
import { IxPushCard } from "@siemens/ix-react";
import { iconBulb } from "@siemens/ix-icons/icons";

export default function NodeCard({ node, depth, onAddChild, onToggleRootCause, onNavigate }) {
    const [open, setOpen] = useState(true);
    const [newChild, setNewChild] = useState("");
    const [isFocused, setIsFocused] = useState(false);
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
            ]
                .filter(Boolean)
                .join(" · "),
        [node.createdAt, node.authorName]
    );

    const toggle = () => {
        if (!hasChildren) return;
        setOpen((v) => !v);
    };

    const resizeTextarea = () => {
        const el = textareaRef.current;
        if (!el) return;
        el.style.height = "auto";
        el.style.height = `${el.scrollHeight}px`;
    };

    const submitChild = () => {
        const value = newChild.trim();
        if (!value) return;

        onAddChild?.(node.id, value);

        setNewChild("");
        setOpen(true);
        requestAnimationFrame(resizeTextarea);
    };

    const onTextareaKeyDown = (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            submitChild();
        }
    };

    const onTextareaChange = (e) => {
        setNewChild(e.target.value);
        resizeTextarea();
    };

    const handleRootCauseClick = (e) => {
        e.stopPropagation();
        onToggleRootCause?.(node.id, !node.isRootCause);
    };

    const handleNavigateClick = (e) => {
        e.stopPropagation();
        onNavigate?.(node.id);
    };

    const rootColor = "#dc3545";
    
    const isRoot = Boolean(node.isRootCause); 
    
    const isValidInput = newChild.trim().length > 0;

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
                    borderColor: isRoot ? rootColor : undefined,
                }}
            >
                <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
                    <div style={{ display: "flex", justifyContent: "flex-end", gap: 8, paddingTop: 16 }}>
                        {isRoot && (
                            <button
                                type="button"
                                onClick={handleNavigateClick}
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 6,
                                    padding: "6px 14px",
                                    fontSize: 12,
                                    fontWeight: 600,
                                    borderRadius: 20,
                                    cursor: "pointer",
                                    transition: "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)",
                                    border: "1px solid rgba(255,255,255,0.3)",
                                    background: "rgba(255,255,255,0.05)",
                                    color: "inherit",
                                    outline: "none",
                                }}
                            >
                                <span>↗</span>
                                <span>Details</span>
                            </button>
                        )}

                        <button
                            type="button"
                            onClick={handleRootCauseClick}
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: 6,
                                padding: "6px 14px",
                                fontSize: 12,
                                fontWeight: 600,
                                borderRadius: 20,
                                cursor: "pointer",
                                transition: "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)",
                                border: `1px solid ${isRoot ? rootColor : "rgba(255,255,255,0.3)"}`,
                                background: isRoot ? rootColor : "rgba(255,255,255,0.05)",
                                color: isRoot ? "#fff" : "inherit",
                                outline: "none",
                                paddingTop: "6px"
                            }}
                        >
                            <span>{isRoot ? "★" : "☆"}</span>
                            <span>{isRoot ? "Root Cause" : "Mark as Root Cause"}</span>
                        </button>
                    </div>

                    <div
                        onClick={(e) => e.stopPropagation()}
                        style={{
                            display: "flex",
                            gap: 12,
                            alignItems: "center",
                            justifyContent: "center",
                            width: "100%",
                            paddingTop: 12,
                            borderTop: "1px solid rgba(255,255,255,0.1)",
                        }}
                    >
                        <div style={{ flex: 1, position: "relative", height: 47 }}>
                            <textarea
                                ref={textareaRef}
                                value={newChild}
                                placeholder="Add a new child cause..."
                                rows={1}
                                onFocus={() => setIsFocused(true)}
                                onBlur={() => setIsFocused(false)}
                                onChange={onTextareaChange}
                                onKeyDown={onTextareaKeyDown}
                                style={{
                                    width: "100%",
                                    minHeight: 44,
                                    resize: "none",
                                    padding: "12px 16px",
                                    fontSize: 14,
                                    borderRadius: 12,
                                    border: `1px solid ${isFocused ? "rgba(255,255,255,0.5)" : "rgba(255,255,255,0.15)"}`,
                                    background: isFocused ? "rgba(255,255,255,0.1)" : "rgba(255,255,255,0.05)",
                                    color: "inherit",
                                    outline: "none",
                                    lineHeight: "1.5",
                                    overflow: "hidden",
                                    boxSizing: "border-box",
                                    transition: "all 0.2s ease",
                                    fontFamily: "inherit",
                                    marginLeft: 6
                                }}
                            />
                        </div>

                        <button
                            type="button"
                            onClick={(e) => {
                                e.stopPropagation();
                                submitChild();
                            }}
                            disabled={!isValidInput}
                            style={{
                                height: 44,
                                padding: "0 20px",
                                fontSize: 14,
                                fontWeight: 600,
                                borderRadius: 12,
                                cursor: isValidInput ? "pointer" : "not-allowed",
                                border: "none",
                                background: isValidInput ? "#007bff" : "rgba(255,255,255,0.1)",
                                color: isValidInput ? "#fff" : "rgba(255,255,255,0.3)",
                                boxSizing: "border-box",
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                transition: "all 0.2s ease",
                                boxShadow: isValidInput ? "0 4px 12px rgba(0,123,255,0.3)" : "none",
                                transform: isValidInput && isFocused ? "translateY(-1px)" : "none",
                            }}
                        >
                            Add
                        </button>
                    </div>
                </div>
            </IxPushCard>

            {hasChildren && open && (
                <div style={{ position: "relative" }}>
                    {node.children.map((child) => (
                        <NodeCard
                            key={child.id}
                            node={child}
                            depth={depth + 1}
                            onAddChild={onAddChild}
                            onToggleRootCause={onToggleRootCause}
                            onNavigate={onNavigate}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}