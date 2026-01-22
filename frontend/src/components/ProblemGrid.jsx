import React, { useState, useEffect } from "react";
import { AgGridReact } from "ag-grid-react";
import * as agGrid from "ag-grid-community";
import { getIxTheme } from "@siemens/ix-aggrid";
import { apiFetch } from "../api/client";
import { useNavigate } from "react-router-dom";
import {
    IxIcon
} from "@siemens/ix-react";
import {
    iconEye
} from "@siemens/ix-icons/icons";

const ixTheme = getIxTheme(agGrid);

const ViewActionRenderer = (params) => {
    const navigate = useNavigate();

    const handleClick = () => {
        console.log(params.data.id)
        navigate(`/problems/${params.data.id}`);
    };

    return (
        <div style={{ display: "flex", alignItems: "center", justifyContent: "center", height: "100%" }}>
            <button
                onClick={handleClick}
                style={{
                    border: "none",
                    background: "transparent",
                    cursor: "pointer",
                    fontSize: "1.2rem",
                    display:"flex"
                }}
                title="Check Root Causes Analysis"
            >
                <IxIcon name={iconEye}  size="32"></IxIcon>
            </button>
        </div>
    );
};

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
        {
            headerName: "Root Cause Analysis",
            field: "action",
            width: 180,
            minWidth: 80,
            pinned: "right",
            sortable: false,
            filter: false,
            cellStyle: { 
                display: 'flex', 
                alignItems: 'center', 
                justifyContent: 'center' 
            },
            cellRenderer: ViewActionRenderer
        }
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

                rowHeight={48}
            />
        </div>
    );
}