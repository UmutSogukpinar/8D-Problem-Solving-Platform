import React, {useState, useEffect } from "react";
import { AgGridReact } from "ag-grid-react";

import "ag-grid-community/styles/ag-grid.css";
import "ag-grid-community/styles/ag-theme-alpine.css";

export default function ProblemsGrid()
{
	const [rowData, setRowData] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);

	const [columnDefs] = useState(() => ([

        { 
            field: "id", 
            headerName: "ID", 
            width: 90, 
            suppressSizeToFit: true 
        },
        
        { 
            field: "title", 
            headerName: "Title",
            sortable: true, 
            filter: true, 
            flex: 1,
            minWidth: 150 
        },

        { 
            field: "description", 
            headerName: "Description",
            sortable: true, 
            filter: true, 
            flex: 2,
            minWidth: 300,
            wrapText: true,
            autoHeight: true
        },
        
        { 
            field: "createdAt", 
            headerName: "Created At", 
            sortable: true, 
            filter: true, 
            width: 180,
            minWidth: 150
        }
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

				const res = await fetch("http://localhost/8d/problems", {
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
		<div className="ag-theme-alpine" style={{ height: "70vh", width: "80%"}}>
			<AgGridReact
				theme="themeQuartz"
				rowData={rowData}
				columnDefs={columnDefs}
				loading={loading}
				pagination={true}
				paginationPageSize={20}
			/>
		</div>
	);
}