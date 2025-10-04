import React, { useState, useEffect, useMemo } from 'react';
import TaskColumn from './TaskColumn';

const TASK_STATUSES = {
  todo: 'To Do',
  in_progress: 'In Progress',
  blocked: 'Blocked',
  done: 'Done',
};

const TaskDashboard = () => {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // TODO: Replace with a real API call.
  // This is a good place to use a custom hook like `useTasks` to encapsulate data fetching logic.

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

  const tasksByStatus = useMemo(() => {
    const groupedTasks = {};
    for (const status in TASK_STATUSES) {
      groupedTasks[status] = tasks.filter(task => task.status === status);
    }
    return groupedTasks;
  }, [tasks]);

  if (loading) {
    return <div className="text-center p-5"><div className="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900" role="status"></div></div>;
  }

  if (error) {
    return <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{error}</div>;
  }

  return (
    <div className="container-fluid mx-auto mt-4">
      <h2 className="mb-4">Task Dashboard</h2>
      
      <div className="flex flex-nowrap overflow-x-auto" style={{ gap: '1rem', padding: '1rem 0' }}>
        {Object.entries(TASK_STATUSES).map(([statusKey, statusLabel]) => (
          <TaskColumn
            key={statusKey}
            title={statusLabel}
            tasks={tasksByStatus[statusKey] || []}
            onStatusChange={handleStatusChange}
            style={{ minWidth: '300px' }}
          />
        ))}
      </div>
    </div>
  );
};

export default TaskDashboard;
