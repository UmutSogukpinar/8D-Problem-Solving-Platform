import { useState, useEffect } from "react";
import { Outlet, useNavigate } from "react-router-dom";
import { apiFetch } from "../api/client";
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
  iconCertificateError,
} from "@siemens/ix-icons/icons";

export default function AppFrame() {
  const [schema, setSchema] = useState("dark");
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const root = document.documentElement;
    root.setAttribute("data-ix-color-schema", schema);
    root.setAttribute("data-ix-theme", "classic");
  }, [schema]);

  useEffect(() => {
    let cancelled = false;

    async function loadMe() {
      try {
        const me = await apiFetch("/8d/me");

        if (!cancelled) setUser(me);
      } catch {
        if (!cancelled) setUser(null);
      }
    }

    loadMe();

    return () => {
      cancelled = true;
    };
  }, []);

  const username = user?.user_name ?? "Guest";
  const extra = user?.crew?.crew_name ?? "User";

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
          icon={iconCertificateError}
          onClick={() => navigate("/problem/dashboard")}
        >
          Problems
        </IxMenuItem>
      </IxMenu>

      <IxContent>
        <IxContentHeader slot="header" headerTitle="My Content Page" />
        <Outlet />
      </IxContent>
    </IxApplication>
  );
}
