import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function ProtectedRoute({ requireOnboarding = false }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen bg-midnight flex items-center justify-center text-gold font-poppins">
        Loading Self Review...
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (requireOnboarding && !user.onboarding_completed) {
    return <Navigate to="/onboarding" replace />;
  }

  if (!requireOnboarding && user.onboarding_completed) {
    return <Navigate to="/home" replace />;
  }

  return <Outlet />;
}