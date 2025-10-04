import React from 'react';
import TaskCard from './TaskCard';

const TaskColumn = ({ title, tasks, onStatusChange }) => {
  return (
    <div className="w-full">
      <div className="bg-white rounded-lg shadow-md h-full">
        <div className="bg-gray-100 p-3 rounded-t-lg border-b flex justify-between items-center">
          <h5 className="mb-0 font-semibold">{title}</h5>
          <span className="bg-blue-500 text-white rounded-full px-2 py-1 text-xs font-semibold">{tasks.length}</span>
        </div>
        <div className="p-3 overflow-y-auto" style={{ maxHeight: 'calc(100vh - 200px)' }}>
          {tasks.length > 0 ? (
            tasks.map(task => (
              <TaskCard 
                key={task.id} 
                task={task} 
                onStatusChange={onStatusChange} 
              />
            ))
          ) : (
            <p className="text-gray-500 text-center">No tasks</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default TaskColumn;