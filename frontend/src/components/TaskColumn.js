import React from 'react';
import TaskCard from './TaskCard';

const TaskColumn = ({ title, tasks, onStatusChange }) => {
  return (
    <div className="col">
      <div className="card h-100">
        <div className="card-header bg-light">
          <h5 className="mb-0">{title}</h5>
          <span className="badge bg-primary">{tasks.length}</span>
        </div>
        <div className="card-body overflow-auto" style={{ maxHeight: 'calc(100vh - 200px)' }}>
          {tasks.length > 0 ? (
            tasks.map(task => (
              <TaskCard 
                key={task.id} 
                task={task} 
                onStatusChange={onStatusChange} 
              />
            ))
          ) : (
            <p className="text-muted text-center">No tasks</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default TaskColumn;