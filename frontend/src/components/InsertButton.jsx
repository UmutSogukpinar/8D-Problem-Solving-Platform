import { IxButton } from "@siemens/ix-react";
import "./css/button.css"
import { useNavigate } from "react-router-dom";

const InsertButton = () => {

    const navigate = useNavigate();

  return (
    <>
      <IxButton variant="primary" onClick={() => {navigate("insert")}}>
        Create New Problem
      </IxButton>
    </>
  );
};

export default InsertButton;
