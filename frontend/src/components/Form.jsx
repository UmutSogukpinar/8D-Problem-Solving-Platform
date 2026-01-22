import React, { useMemo, useState } from "react";
import {
	IxLayoutAuto,
	IxFieldLabel,
	IxSelect,
	IxSelectItem,
	IxInput,
	IxButton,
} from "@siemens/ix-react";
import { apiFetch } from "../api/client";

export default function Form({ crews, user }) 
{
	const [crewId, setCrewId] = useState("");
	const [title, setTitle] = useState("");
	const [description, setDescription] = useState("");

	const [submitting, setSubmitting] = useState(false);
	const [error, setError] = useState(null);
	const [success, setSuccess] = useState(false);

	const crewOptions = useMemo(() => {
		return ((crews || []).map((c) => ({
			id: String(c.id),
			name: c.name,
		})));
	}, [crews]);

	function onCrewChange(e) {
		const value = e?.detail?.value ?? e?.detail ?? "";
		setCrewId(String(value));
	}

	function onTitleChange(e) {
		const value = e?.target?.value ?? e?.detail?.value ?? "";
		setTitle(value);
	}

	function onDescriptionChange(e) {
		const value = e?.target?.value ?? e?.detail?.value ?? "";
		setDescription(value);
	}

	async function onSubmit() {
		try {
			setSubmitting(true);
			setError(null);
			setSuccess(false);

			if (!crewId)
				throw new Error("Please select a crew.");

			if (!title.trim())
				throw new Error("Title is required.");

			if (!description.trim())
				throw new Error("Description is required.");


			await apiFetch("/8d/problems", {
				method: "POST",
				body: JSON.stringify({
					user_id: Number(user.userId),
					crew_id: Number(crewId),
					title: title.trim(),
					description: description.trim(),
				}),
			});

			setSuccess(true);
			setTitle("");
			setDescription("");
			setCrewId("");
		}
		catch (e) 
		{
			setError(e.message ?? "Submit failed");
		}
		finally 
		{
			setSubmitting(false);
		}
	}

	return (
		<IxLayoutAuto>
			<IxFieldLabel htmlFor="crew-select">Assigned Crew</IxFieldLabel>

			<IxSelect
				id="crew-select"
				value={crewId}
				onValueChange={onCrewChange}
				disabled={submitting}
			>
				{crewOptions.map((crew) => (
					<IxSelectItem key={crew.id} value={crew.id} label={crew.name}>
						{crew.name}
					</IxSelectItem>
				))}
			</IxSelect>

			<IxFieldLabel htmlFor="problem-title">Title</IxFieldLabel>
			<IxInput
				id="problem-title"
				value={title}
				onInput={onTitleChange}
				disabled={submitting}
			/>

			<IxFieldLabel htmlFor="problem-desc">Description</IxFieldLabel>
			<IxInput
				id="problem-desc"
				value={description}
				onInput={onDescriptionChange}
				disabled={submitting}
			/>

			<IxButton
				data-colspan="2"
				variant="primary"
				onClick={onSubmit}
				disabled={submitting}
			>
				{submitting ? "Submitting..." : "Submit"}
			</IxButton>

			{error ? (
				<div style={{ color: "red" }} data-colspan="2">{error}</div>
			) : null}

			{success ? (
				<div style={{ color: "lime" }} data-colspan="2">Saved.</div>
			) : null}
		</IxLayoutAuto>
	);
}
