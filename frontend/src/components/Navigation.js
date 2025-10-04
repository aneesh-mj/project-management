import React, { useContext, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

const Navigation = () => {
  const { currentUser, logout } = useContext(AuthContext);
  const navigate = useNavigate();
  const [isOpen, setIsOpen] = useState(false);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <nav className="bg-blue-500 p-4 mb-4">
      <div className="container mx-auto flex justify-between items-center">
        <Link className="text-white text-xl font-bold" to="/">Project Management</Link>
        <div className="md:hidden">
          <button onClick={() => setIsOpen(!isOpen)} className="text-white">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
          </button>
        </div>
        <div className={`md:flex ${isOpen ? 'block' : 'hidden'} w-full md:w-auto`}>
          {currentUser && (
            <ul className="flex flex-col md:flex-row md:space-x-4">
              <li>
                <Link className="text-white block py-2 md:py-0" to="/dashboard">Dashboard</Link>
              </li>
              <li>
                <Link className="text-white block py-2 md:py-0" to="/tasks">Tasks</Link>
              </li>
            </ul>
          )}
          <div className="flex items-center mt-4 md:mt-0">
            {currentUser ? (
              <div className="flex items-center">
                <span className="text-white mr-3">Welcome, {currentUser.first_name}</span>
                <button className="bg-transparent hover:bg-white text-white font-semibold hover:text-blue-500 py-2 px-4 border border-white hover:border-transparent rounded" onClick={handleLogout}>Logout</button>
              </div>
            ) : (
              <Link className="bg-transparent hover:bg-white text-white font-semibold hover:text-blue-500 py-2 px-4 border border-white hover:border-transparent rounded" to="/login">Login</Link>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navigation;