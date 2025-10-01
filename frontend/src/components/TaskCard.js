import React from 'react';

const TaskCard = ({ task, onStatusChange }) => {
  const statusOptions = ['todo', 'in_progress', 'blocked', 'done'];
  
  return (
    <div className="card mb-3">
      <div className="card-body">
        <h5 className="card-title">{task.title}</h5>
        <p className="card-text">{task.description}</p>
        <div className="d-flex justify-content-between align-items-center">
          <select 
            className="form-select form-select-sm w-50"
            value={task.status}
            onChange={(e) => onStatusChange(task.id, e.target.value)}
          >
            {statusOptions.map(status => (
              <option key={status} value={status}>
                {status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
              </option>
            ))}
          </select>
          <span className="badge bg-secondary">{task.due_date}</span>
        </div>
        <div className="mt-2">
          <small className="text-muted">Assigned to: {task.assigned_to}</small>
        </div>
      </div>
    </div>
  );
};

export default TaskCard;