import React, { useState, useEffect, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import TaskColumn from './TaskColumn';

const TaskDashboard = () => {
  const { currentUser } = useContext(AuthContext);
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Mock data for now - will be replaced with API call
  useEffect(() => {
    // Simulate API call
    setTimeout(() => {
      const mockTasks = [
        { id: 1, title: 'Design Database Schema', description: 'Create ERD for the project', status: 'done', due_date: '2023-10-15', assigned_to: 'John Doe' },
        { id: 2, title: 'Setup API Routes', description: 'Configure all necessary API endpoints', status: 'in_progress', due_date: '2023-10-20', assigned_to: 'Jane Smith' },
        { id: 3, title: 'Implement Authentication', description: 'Add JWT authentication', status: 'todo', due_date: '2023-10-25', assigned_to: 'Mike Johnson' },
        { id: 4, title: 'Create Frontend Components', description: 'Build React components', status: 'blocked', due_date: '2023-10-18', assigned_to: 'Sarah Williams' },
        { id: 5, title: 'Write Unit Tests', description: 'Create tests for backend', status: 'todo', due_date: '2023-10-30', assigned_to: 'John Doe' },
        { id: 6, title: 'Deploy to Staging', description: 'Setup staging environment', status: 'todo', due_date: '2023-11-05', assigned_to: 'Jane Smith' },
      ];
      setTasks(mockTasks);
      setLoading(false);
    }, 1000);
  }, []);

  const handleStatusChange = (taskId, newStatus) => {
    setTasks(prevTasks => 
      prevTasks.map(task => 
        task.id === taskId ? { ...task, status: newStatus } : task
      )
    );
    
    // Here you would make an API call to update the task status
    console.log(`Task ${taskId} status changed to ${newStatus}`);
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border" role="status"></div></div>;
  }

  if (error) {
    return <div className="alert alert-danger">{error}</div>;
  }

  // Filter tasks by status
  const todoTasks = tasks.filter(task => task.status === 'todo');
  const inProgressTasks = tasks.filter(task => task.status === 'in_progress');
  const blockedTasks = tasks.filter(task => task.status === 'blocked');
  const doneTasks = tasks.filter(task => task.status === 'done');

  return (
    <div className="container-fluid mt-4">
      <h2 className="mb-4">Task Dashboard</h2>
      
      <div className="row d-flex flex-row flex-nowrap overflow-auto">
        <TaskColumn 
          title="To Do" 
          tasks={todoTasks} 
          onStatusChange={handleStatusChange} 
        />
        <TaskColumn 
          title="In Progress" 
          tasks={inProgressTasks} 
          onStatusChange={handleStatusChange} 
        />
        <TaskColumn 
          title="Blocked" 
          tasks={blockedTasks} 
          onStatusChange={handleStatusChange} 
        />
        <TaskColumn 
          title="Done" 
          tasks={doneTasks} 
          onStatusChange={handleStatusChange} 
        />
      </div>
    </div>
  );
};

export default TaskDashboard;