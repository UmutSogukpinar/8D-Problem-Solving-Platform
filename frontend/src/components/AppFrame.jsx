import { useState, useEffect } from "react";
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
import { iconSun, iconMoon } from "@siemens/ix-icons/icons";
import {
	iconHome,
    iconCertificateError
} from '@siemens/ix-icons/icons';


export default function AppFrame() 
{
  const [schema, setSchema] = useState("dark");

  useEffect(() => {
    const root = document.documentElement;
    root.setAttribute("data-ix-color-schema", schema);
    root.setAttribute("data-ix-theme", "classic");
  }, [schema]);

  const toggleTheme = () => {
    setSchema((prev) => (prev === "dark" ? "light" : "dark"));
  };

  return (
    <IxApplication>
  
      <IxApplicationHeader name="My Application">
        <div className="placeholder-logo" slot="logo"></div>

        <IxIconButton
          variant="tertiary"
          icon={schema === "dark" ? iconSun : iconMoon}
          onClick={toggleTheme}
          title={
            schema === "dark" ? "Switch to light mode" : "Switch to dark mode"
          }
        />

        <IxAvatar
          username="John Doe"
          extra="User"
          image="https://www.gravatar.com/avatar/00000000000000000000000000000000"
        />

      </IxApplicationHeader>

      <IxMenu>
        <IxMenuItem icon={iconHome}>Main Menu</IxMenuItem>
        <IxMenuItem icon={iconCertificateError}>DashBoard</IxMenuItem>
      </IxMenu>

      <IxContent>
        <IxContentHeader slot="header" headerTitle="My Content Page" />
      </IxContent>
    </IxApplication>
  );
}
