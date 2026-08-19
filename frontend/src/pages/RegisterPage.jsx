import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function RegisterPage() {
  const { register } = useAuth();
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    first_name: '',
    email: '',
    phone: '',
    password: ''
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await register(formData);
      navigate('/onboarding');
    } catch (err) {
      // Extracts explicit error message returned by PHP backend
      const errorMessage =
        err.response?.data?.message ||
        err.response?.data?.error ||
        err.message ||
        'Registration failed. Please check your details and try again.';

      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <h2 className="text-2xl font-semibold mb-6 text-center text-slate-100">Create Account</h2>

      {error && (
        <div className="bg-red-500/10 border border-red-500 text-red-400 text-sm p-3 rounded-xl mb-4 text-center">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-xs uppercase font-medium text-slate-300 mb-1">First Name</label>
          <input
            type="text"
            name="first_name"
            required
            value={formData.first_name}
            onChange={handleChange}
            placeholder="John"
            className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue"
          />
        </div>

        <div>
          <label className="block text-xs uppercase font-medium text-slate-300 mb-1">Email Address</label>
          <input
            type="email"
            name="email"
            required
            value={formData.email}
            onChange={handleChange}
            placeholder="john@example.com"
            className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue"
          />
        </div>

        <div>
          <label className="block text-xs uppercase font-medium text-slate-300 mb-1">Phone Number</label>
          <input
            type="tel"
            name="phone"
            required
            value={formData.phone}
            onChange={handleChange}
            placeholder="+234..."
            className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue"
          />
        </div>

        <div>
          <label className="block text-xs uppercase font-medium text-slate-300 mb-1">Password</label>
          <input
            type="password"
            name="password"
            required
            minLength={6}
            value={formData.password}
            onChange={handleChange}
            placeholder="••••••••"
            className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue"
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full py-3 mt-2 bg-royalBlue hover:bg-blue-600 text-white font-semibold rounded-xl transition duration-200 disabled:opacity-50"
        >
          {loading ? 'Creating Account...' : 'Register'}
        </button>
      </form>

      <p className="text-center text-slate-400 text-sm mt-6">
        Already have an account?{' '}
        <Link to="/login" className="text-softGold font-medium hover:underline">
          Sign In
        </Link>
      </p>
    </div>
  );
}