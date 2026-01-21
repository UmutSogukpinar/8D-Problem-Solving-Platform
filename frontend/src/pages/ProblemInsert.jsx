import { useEffect, useState } from "react";
import Form from "../components/Form";

const ProblemInsert = () => {
  const [crews, setCrews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    async function fetchCrews() {
      try {
        setLoading(true);
        setError(null);

        const res = await fetch("http://localhost/8d/crew");

        if (!res.ok) 
        {
          throw new Error(`HTTP ${res.status}`);
        }

        const json = await res.json();

        setCrews(json);
      } catch (e) {
        setError(e.message ?? "Failed to load crews");
      } finally {
        setLoading(false);
      }
    }

    fetchCrews();
  }, []);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div style={{ color: "red" }}>{error}</div>;
  }

  return <Form crews={crews} />;
};

export default ProblemInsert;
