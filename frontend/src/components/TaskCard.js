import React from 'react';

const TaskCard = ({ task, onStatusChange }) => {
  const statusOptions = ['todo', 'in_progress', 'blocked', 'done'];
  
  return (
    <div className="bg-white rounded-lg shadow-md mb-3">
      <div className="p-4">
        <h5 className="text-lg font-semibold">{task.title}</h5>
        <p className="text-gray-600">{task.description}</p>
        <div className="flex justify-between items-center">
          <select 
            className="form-select text-sm w-1/2"
            value={task.status}
            onChange={(e) => onStatusChange(task.id, e.target.value)}
          >
            {statusOptions.map(status => (
              <option key={status} value={status}>
                {status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
              </option>
            ))}
          </select>
          <span className="bg-gray-400 text-white rounded-full px-2 py-1 text-xs font-semibold">{task.due_date}</span>
        </div>
        <div className="mt-2">
          <small className="text-gray-500">Assigned to: {task.assigned_to}</small>
        </div>
      </div>
    </div>
  );
};

export default TaskCard;