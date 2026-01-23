import { useState, useEffect } from "react";
import { Outlet, useNavigate } from "react-router-dom";
import { useUser } from "../context/UserContext";
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
import {
	iconSun,
	iconMoon,
	iconHome,
	iconAlarmFilled
} from "@siemens/ix-icons/icons";


export default function AppFrame() {
	const [schema, setSchema] = useState("dark");
	const navigate = useNavigate();

	const { user } = useUser();

	useEffect(() => {

		const root = document.documentElement;
		root.setAttribute("data-ix-color-schema", schema);
		root.setAttribute("data-ix-theme", "classic");

	}, [schema]);

	const username = user?.username ?? "Guest";
	const extra = user?.crew?.name ?? "User";

	return (
		<IxApplication>
			<IxApplicationHeader name="My Application">
				<div className="placeholder-logo" slot="logo"></div>

				<IxIconButton
					variant="tertiary"
					icon={schema === "dark" ? iconSun : iconMoon}
					onClick={() =>
						setSchema((prev) => (prev === "dark" ? "light" : "dark"))
					}
					title={
						schema === "dark" ? "Switch to light mode" : "Switch to dark mode"
					}
				/>

				<IxAvatar
					username={username}
					extra={extra}
					image="https://www.gravatar.com/avatar/00000000000000000000000000000000"
				/>
			</IxApplicationHeader>

			<IxMenu>
				<IxMenuItem icon={iconHome} onClick={() => navigate("/")}>
					Home
				</IxMenuItem>
				<IxMenuItem
					icon={iconAlarmFilled}
					onClick={() => navigate("/problem/dashboard")}
				>
					Problems
				</IxMenuItem>
			</IxMenu>

			<IxContent>
				<IxContentHeader slot="header" headerTitle="8D Problem Solving Platform" />
				<Outlet />
			</IxContent>
		</IxApplication>
	);
}
