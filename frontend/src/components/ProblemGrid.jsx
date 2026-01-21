import React, { useState, useEffect } from "react";
import { AgGridReact } from "ag-grid-react";
import * as agGrid from "ag-grid-community";
import { getIxTheme } from "@siemens/ix-aggrid";
import { apiFetch } from "../api/client";

const ixTheme = getIxTheme(agGrid);

export default function ProblemsGrid() {
  const [rowData, setRowData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [columnDefs] = useState(() => [
    {
      field: "id",
      headerName: "ID",
      width: 90,
      suppressSizeToFit: true,
    },
    {
      field: "title",
      headerName: "Title",
      sortable: true,
      filter: true,
      flex: 1,
      minWidth: 150,
    },
    {
      field: "description",
      headerName: "Description",
      sortable: true,
      filter: true,
      flex: 2,
      minWidth: 300,
      wrapText: true,
      autoHeight: true,
    },
    {
      field: "createdAt",
      headerName: "Created At",
      sortable: true,
      filter: true,
      width: 180,
      minWidth: 150,
    },
  ]);

  useEffect(() => {
    let cancelled = false;

    async function load() {
      try {
        setLoading(true);
        setError(null);

        const data = await apiFetch("/8d/problems");

        if (!cancelled)
          setRowData(Array.isArray(data) ? data : (data.data ?? []));
      } catch (e) {
        if (!cancelled) setError(e.message ?? "Request failed");
      } finally {
        if (!cancelled) setLoading(false);
      }
    }

    load();

    return () => {
      cancelled = true;
    };
  }, []);

  if (error) {
    return <div style={{ padding: 12 }}>API Error: {error}</div>;
  }

  return (
    <div style={{ height: "70vh", width: "80%" }}>
      <AgGridReact
        theme={ixTheme}
        rowData={rowData}
        columnDefs={columnDefs}
        loading={loading}
        pagination={true}
        paginationPageSize={20}
      />
    </div>
  );
}