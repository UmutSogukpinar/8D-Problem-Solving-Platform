import { useEffect, useMemo, useState } from "react";
import { useParams } from "react-router-dom";
import {
	IxButton,
	IxCard,
	IxCardContent,
	IxDivider,
	IxLayoutGrid,
	IxRow,
	IxCol,
	IxSpinner,
	IxTypography,
} from "@siemens/ix-react";
import { apiFetch } from "../api/client";

function toUiSolution(s)
{
	return ({
		id: Number(s.id),

		problemId: Number(s.problemId ?? s.problem_id),
		rootCauseId: Number(s.rootCauseId ?? s.root_cause_id),

		rootCauseDescription:
			(s.rootCauseDescription ?? s.root_cause_description ?? null),

		isRootCause:
			(s.isRootCause ?? (typeof s.is_root_cause === "number"
				? s.is_root_cause === 1
				: (typeof s.is_root_cause === "boolean" ? s.is_root_cause : null))),

		description: String(s.description ?? ""),
		createdAt: String(s.createdAt ?? s.created_at ?? ""),
		authorName: String(s.author?.name ?? s.author_name ?? "-"),
	});
}

function normalizeSolutions(payload)
{
	const raw =
		Array.isArray(payload)
			? payload
			: (payload && Array.isArray(payload.solutions))
				? payload.solutions
				: [];

	return (raw.map(toUiSolution));
}

export default function SolutionsListPage()
{
	const { id } = useParams();

	const [solutions, setSolutions] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState("");

	const title = useMemo(() =>
	{
		return (id ? `Solutions for Problem #${id}` : "All Solutions");
	}, [id]);

	async function load()
	{
		setLoading(true);
		setError("");
        console.log(id);
		try
		{
			const path = `/8d/problems/${Number(id)}/solutions`


			const data = await apiFetch(path);
			setSolutions(normalizeSolutions(data));
		}
		catch (e)
		{
			setSolutions([]);
			setError(e?.message || "Failed to load solutions");
		}
		finally
		{
			setLoading(false);
		}
	}

	useEffect(() =>
	{
		load();
	}, [id]);

	return (
		<IxLayoutGrid>
			<IxRow>
				<IxCol size="12">
					<div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", gap: 12 }}>
						<IxTypography variant="h2">
							{title}
						</IxTypography>

						<IxButton variant="secondary" onClick={load} disabled={loading}>
							Refresh
						</IxButton>
					</div>

					<IxDivider style={{ marginTop: 12, marginBottom: 12 }} />
				</IxCol>
			</IxRow>

			{loading && (
				<IxRow>
					<IxCol size="12">
						<div style={{ display: "flex", alignItems: "center", gap: 12 }}>
							<IxSpinner />
							<IxTypography>Loading solutions...</IxTypography>
						</div>
					</IxCol>
				</IxRow>
			)}

			{!loading && error && (
				<IxRow>
					<IxCol size="12">
						<IxCard variant="outline" style={{ borderColor: "var(--theme-color-alarm)" }}>
							<IxCardContent>
								<IxTypography variant="h3">Error</IxTypography>
								<IxTypography>{error}</IxTypography>

								<div style={{ marginTop: 12 }}>
									<IxButton onClick={load}>Try again</IxButton>
								</div>
							</IxCardContent>
						</IxCard>
					</IxCol>
				</IxRow>
			)}

			{!loading && !error && solutions.length === 0 && (
				<IxRow>
					<IxCol size="12">
						<IxCard>
							<IxCardContent>
								<IxTypography variant="h3">No solutions</IxTypography>
								<IxTypography>Nothing to show yet.</IxTypography>
							</IxCardContent>
						</IxCard>
					</IxCol>
				</IxRow>
			)}

			{!loading && !error && solutions.length > 0 && (
				<IxRow>
					<IxCol size="12">
						<div style={{ display: "grid", gap: 12 }}>
							{solutions.map((s) => (
								<IxCard key={s.id} style={{ width: "100%" }}>
									<IxCardContent>
										<div style={{ display: "flex", justifyContent: "space-between", gap: 12, alignItems: "flex-start" }}>
											<div style={{ flex: 1 }}>
												<IxTypography variant="h3">
													Solution #{s.id}
												</IxTypography>

												<IxTypography style={{ opacity: 0.8 }}>
													Problem: {s.problemId} · Root Cause: {s.rootCauseId}
													{s.isRootCause === true ? " · (Root)" : ""}
												</IxTypography>

												{s.rootCauseDescription && (
													<IxTypography style={{ opacity: 0.8 }}>
														Root cause: {s.rootCauseDescription}
													</IxTypography>
												)}

												<IxDivider style={{ marginTop: 10, marginBottom: 10 }} />

												<IxTypography>
													{s.description}
												</IxTypography>
											</div>

											<div style={{ minWidth: 220, textAlign: "right" }}>
												<IxTypography style={{ opacity: 0.8 }}>
													{s.createdAt}
												</IxTypography>

												<IxTypography style={{ opacity: 0.8 }}>
													Author: {s.authorName}
												</IxTypography>
											</div>
										</div>
									</IxCardContent>
								</IxCard>
							))}
						</div>
					</IxCol>
				</IxRow>
			)}
		</IxLayoutGrid>
	);
}
