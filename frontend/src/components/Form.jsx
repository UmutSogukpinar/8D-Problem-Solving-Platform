import React, { useState } from "react";
import {
  IxLayoutAuto,
  IxFieldLabel,
  IxSelect,
  IxSelectItem,
  IxInput,
  IxButton,
} from "@siemens/ix-react";

const Form = ({ crews }) => {
  const [crewName, setCrewName] = useState("");

  const handleCrewChange = (event) => {
    setCrewName(event.detail);
  };

  return (
    <IxLayoutAuto>
      <IxFieldLabel htmlFor="crew-select">Crew</IxFieldLabel>

      <IxSelect 
        id="crew-select" 
        value={crewName} 
        onValueChange={handleCrewChange}
      >
        {crews.map((crew) => (
          <IxSelectItem key={crew.id} value={crew.name} label={crew.name}>
            {crew.name}
          </IxSelectItem>
        ))}
      </IxSelect>

      <IxFieldLabel htmlFor="problem-title">Title</IxFieldLabel>
      <IxInput id="problem-title" />

      <IxFieldLabel htmlFor="problem-desc">Description</IxFieldLabel>
      <IxInput id="problem-desc" />

      <IxButton data-colspan="2">Submit</IxButton>
    </IxLayoutAuto>
  );
};

export default Form;