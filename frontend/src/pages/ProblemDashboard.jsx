import React from 'react'
import ProblemsGrid from '../components/ProblemGrid'
import InsertButton from '../components/InsertButton';

const ProblemDashboard = () =>
{
  return (
    <div
      style={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        width: '100%',
        height: '90%',
      }}
    >
      <ProblemsGrid />
      <InsertButton></InsertButton>
    </div>
  );
};

export default ProblemDashboard;
