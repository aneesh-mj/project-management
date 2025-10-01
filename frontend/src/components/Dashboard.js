import React, { useContext, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { companyService, userService } from '../services/api';

const Dashboard = () => {
  const { currentUser, logout } = useContext(AuthContext);
  const [companies, setCompanies] = useState([]);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const navigate = useNavigate();

  useEffect(() => {

    const fetchData = async () => {
      try {
        setLoading(true);
        // Fetch data based on user role
        if (currentUser.role === 'admin' || currentUser.role === 'manager') {
          const companiesResponse = await companyService.getAllCompanies();
          if (companiesResponse.status === 'success') {
            setCompanies(companiesResponse.data);
          }
          
          const usersResponse = await userService.getAllUsers();
          if (usersResponse.status === 'success') {
            setUsers(usersResponse.data);
          }
        } else {
          // For regular users, only fetch their company
          if (currentUser.company_id) {
            const companyResponse = await companyService.getCompanyById(currentUser.company_id);
            if (companyResponse.status === 'success') {
              setCompanies([companyResponse.data]);
            }
          }
        }
      } catch (err) {
        setError('Failed to load data. Please try again.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [currentUser, navigate]);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      <div className="header">
        <h1>Project Management Dashboard</h1>
        <div>
          <span>Welcome, {currentUser?.name || 'User'} ({currentUser?.role})</span>
          <button onClick={handleLogout} className="btn" style={{ marginLeft: '10px' }}>Logout</button>
        </div>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      <div className="dashboard-content">
        {/* Companies Section */}
        <div className="section">
          <h2>Companies</h2>
          {companies.length > 0 ? (
            <div className="card-grid">
              {companies.map(company => (
                <div key={company.id} className="card">
                  <h3>{company.name}</h3>
                  <p>{company.description}</p>
                </div>
              ))}
            </div>
          ) : (
            <p>No companies available.</p>
          )}
        </div>

        {/* Users Section - Only for admin/manager */}
        {(currentUser?.role === 'admin' || currentUser?.role === 'manager') && (
          <div className="section">
            <h2>Users</h2>
            {users.length > 0 ? (
              <div className="card-grid">
                {users.map(user => (
                  <div key={user.id} className="card">
                    <h3>{user.name}</h3>
                    <p>Email: {user.email}</p>
                    <p>Role: {user.role}</p>
                  </div>
                ))}
              </div>
            ) : (
              <p>No users available.</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default Dashboard;