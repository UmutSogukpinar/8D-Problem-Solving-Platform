import { Routes, Route, Navigate } from "react-router-dom";
import AppFrame from "./components/AppFrame";
import ProblemDashboard from "./pages/ProblemDashboard";
import ProblemInsert from "./pages/ProblemInsert";
import ProblemDetail from "./pages/ProblemDetail";
import SolutionForm from "./pages/SolutionForm";
import SolutionsListPage from "./pages/SolutionListPage"

function Home()
{
	return (<div>Home</div>);
}

function NotFound()
{
	return (<div>404</div>);
}

export default function App()
{
	return (
		<Routes>
			<Route element={<AppFrame />}>
				<Route path="/" element={<Home />} />
				<Route path="/problem/dashboard" element={<ProblemDashboard />} />
				<Route path="/problem/dashboard/insert" element={<ProblemInsert />} />
				<Route path="/problems/:id" element={<ProblemDetail />} />

				<Route path="/root-causes/:id" element={<SolutionForm />} />

				<Route path="/problems/:id/solutions" element={<SolutionsListPage />} />

				<Route path="/404" element={<NotFound />} />
				<Route path="*" element={<Navigate to="/404" replace />} />
			</Route>
		</Routes>
	);
}
