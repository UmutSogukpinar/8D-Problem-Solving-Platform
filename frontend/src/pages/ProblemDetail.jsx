import React from 'react'
import { useParams } from 'react-router-dom'
import CauseTree from '../components/CauseTree';

const ProblemDetail = () => {

    const {id} = useParams();

  return (
    <div>
        <CauseTree problemId={id} />
    </div>
  )
}

export default ProblemDetail