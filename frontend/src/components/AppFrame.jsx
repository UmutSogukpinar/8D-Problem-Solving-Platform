import { useState, useEffect } from "react";
import { Outlet, useNavigate } from "react-router-dom";
import {
	IxApplication,
	IxApplicationHeader,
	IxContent,
	IxContentHeader,
	IxMenu,
	IxMenuItem,
	IxIconButton,
	IxAvatar,
} from "@siemens/ix-react";
import { iconSun, iconMoon, iconHome, iconCertificateError } from "@siemens/ix-icons/icons";

export default function AppFrame()
{
	const [schema, setSchema] = useState("dark");
	const navigate = useNavigate();

	useEffect(() =>
	{
		const root = document.documentElement;
		root.setAttribute("data-ix-color-schema", schema);
		root.setAttribute("data-ix-theme", "classic");
	}, [schema]);

	function toggleTheme()
	{
		setSchema((prev) => (prev === "dark" ? "light" : "dark"));
	}

	return (
		<IxApplication>
			<IxApplicationHeader name="My Application">
				<div className="placeholder-logo" slot="logo"></div>

				<IxIconButton
					variant="tertiary"
					icon={schema === "dark" ? iconSun : iconMoon}
					onClick={toggleTheme}
					title={schema === "dark" ? "Switch to light mode" : "Switch to dark mode"}
				/>

				<IxAvatar
					username="John Doe"
					extra="User"
					image="https://www.gravatar.com/avatar/00000000000000000000000000000000"
				/>
			</IxApplicationHeader>

			<IxMenu>
				<IxMenuItem icon={iconHome} onClick={() => navigate("/")}>Home</IxMenuItem>
				<IxMenuItem icon={iconCertificateError} onClick={() => navigate("/problem/dashboard")}>Problems</IxMenuItem>
			</IxMenu>

			<IxContent>
				<IxContentHeader slot="header" headerTitle="My Content Page" />
				<Outlet />
			</IxContent>
		</IxApplication>
	);
}
