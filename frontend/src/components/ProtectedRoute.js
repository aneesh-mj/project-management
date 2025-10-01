import React, { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

const ProtectedRoute = ({ children }) => {
  const { currentUser, loading } = useContext(AuthContext);
  
  // Check localStorage directly as a fallback
  const userFromStorage = localStorage.getItem('user');
  
  // Show loading state while authentication is being checked
  if (loading) {
    return <div>Loading...</div>;
  }
  
  // Redirect to login if not authenticated
  if (!currentUser && !userFromStorage) {
    return <Navigate to="/login" />;
  }
  
  // Render children if authenticated
  return children;
};

export default ProtectedRoute;