import { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(localStorage.getItem('sr_token'));
  const [loading, setLoading] = useState(true);

  // Configure global Axios token
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  } else {
    delete axios.defaults.headers.common['Authorization'];
  }

  useEffect(() => {
    const fetchUser = async () => {
      if (!token) {
        setLoading(false);
        return;
      }
      try {
        const res = await axios.get(`${API_BASE}/auth/me`);
        setUser(res.data.data.user);
      } catch (err) {
        logout();
      } finally {
        setLoading(false);
      }
    };

    fetchUser();
  }, [token]);

  const login = async (identifier, password) => {
    const res = await axios.post(`${API_BASE}/auth/login`, { identifier, password });
    const { token: newToken, user: userData } = res.data.data;
    localStorage.setItem('sr_token', newToken);
    setToken(newToken);
    setUser(userData);
    return userData;
  };

  const register = async (formData) => {
    const res = await axios.post(`${API_BASE}/auth/register`, formData);
    const { token: newToken, user: userData } = res.data.data;
    localStorage.setItem('sr_token', newToken);
    setToken(newToken);
    setUser(userData);
    return userData;
  };

  const logout = () => {
    localStorage.removeItem('sr_token');
    setToken(null);
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout, isAuthenticated: !!user }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);