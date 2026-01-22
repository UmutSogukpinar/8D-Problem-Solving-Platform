export function buildTree(nodes)
{
    const byId = new Map();
    const roots = [];

    for (const n of (nodes || []))
    {
        byId.set(n.id, { ...n, children: [] });
    }

    for (const n of (nodes || []))
    {
        const current = byId.get(n.id);

        if (n.parentId == null)
        {
            roots.push(current);
            continue;
        }

        const parent = byId.get(n.parentId);

        if (parent)
        {
            parent.children.push(current);
            continue;
        }

        roots.push(current);
    }

    return (roots);
}
