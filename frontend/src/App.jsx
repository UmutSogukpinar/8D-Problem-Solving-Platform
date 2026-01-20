import { Routes, Route, Navigate } from "react-router-dom";
import AppFrame from "./components/AppFrame";

function Home() {
  return <div>Home</div>;
}

function Problems() {
  return <div>Problems</div>;
}

function NotFound() {
  return <div>404</div>;
}

export default function App() {
  return (
    <Routes>
      <Route element={<AppFrame />}>
        <Route path="/" element={<Home />} />
        <Route path="/dashboard" element={<Problems />} />
        <Route path="/404" element={<NotFound />} />
        <Route path="*" element={<Navigate to="/404" replace />} />
      </Route>
    </Routes>
  );
}
