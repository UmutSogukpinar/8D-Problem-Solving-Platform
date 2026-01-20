import React, { useCallback, useMemo, useRef, useState, useEffect } from "react";
import { AgGridReact } from "ag-grid-react";

import "ag-grid-community/styles/ag-grid.css";
import "ag-grid-community/styles/ag-theme-alpine.css";

export default function ProblemsGrid()
{
	const [rowData, setRowData] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);

	const [columnDefs] = useState(() => ([
		{ field: "id", maxWidth: 90 },
		{ field: "title", sortable: true, filter: true, flex: 1 },
		{ field: "status", sortable: true, filter: true, maxWidth: 140 },
		{ field: "created_at", headerName: "Created", sortable: true, filter: true, maxWidth: 160 }
	]));

	useEffect(() =>
	{
		let cancelled = false;

		async function load()
		{
			try
			{
				setLoading(true);
				setError(null);

				// TODO: Put real endpoint here
				const res = await fetch("http://localhost/problems", {
					headers: { "Accept": "application/json" }
				});

				if (!res.ok)
					throw new Error(`HTTP ${res.status}`);

				const data = await res.json();

				if (!cancelled)
					setRowData(Array.isArray(data) ? data : data.data ?? []);
			}
			catch (e)
			{
				if (!cancelled)
					setError(e.message ?? "Request failed");
			}
			finally
			{
				if (!cancelled)
					setLoading(false);
			}
		}

		load();

		return () => { cancelled = true; };
	}, []);

	if (error)
		return <div style={{ padding: 12 }}>API Error: {error}</div>;

	return (
		<div className="ag-theme-alpine" style={{ height: "70vh", width: "100%" }}>
			<AgGridReact
				rowData={rowData}
				columnDefs={columnDefs}
				loading={loading}
				pagination={true}
				paginationPageSize={20}
			/>
		</div>
	);
}