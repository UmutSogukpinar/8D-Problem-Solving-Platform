import React from "react";
import ProblemsGrid from "../components/ProblemGrid";
import InsertButton from "../components/InsertButton";

const ProblemDashboard = () =>
{
	return (
		<div
			style={{
				padding: 16,
				background: "var(--ix-background-color)",
				height: "100%",
			}}
		>
			<div
				style={{
					background: "var(--ix-card-background)",
					borderRadius: 8,
					padding: 16,
					boxShadow: "0 1px 4px rgba(0,0,0,0.15)",
				}}
			>
				{/* HEADER */}
				<div
					style={{
						display: "flex",
						justifyContent: "space-between",
						alignItems: "center",
						marginBottom: 12,
					}}
				>
					<div>
						<h2 style={{ margin: 0 }}>Problems</h2>
						<span style={{ fontSize: 12, opacity: 0.7 }}>
							List of reported problems
						</span>
					</div>

					<InsertButton />
				</div>

				{/* DIVIDER */}
				<div
					style={{
						height: 1,
						background: "var(--ix-border-color)",
						marginBottom: 12,
					}}
				/>

				{/* CONTENT */}
				<ProblemsGrid />
			</div>
		</div>
	);
};

export default ProblemDashboard;
