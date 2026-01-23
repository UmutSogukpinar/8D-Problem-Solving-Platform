import React, { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import {
    IxButton,
    IxCard,
    IxCardContent,
    IxDivider,
    IxLayoutGrid,
    IxRow,
    IxCol,
    IxTextarea,
    IxTypography,
    IxSpinner,
    IxIcon
} from '@siemens/ix-react'
import { apiFetch } from '../api/client'
import { useUser } from '../context/UserContext'
import { addIcons } from '@siemens/ix-icons'
import {
    iconChevronLeft,
    iconInfo,
    iconUser,
    iconCalendar,
    iconBulb
} from '@siemens/ix-icons/icons'

addIcons({
    'chevron-left': iconChevronLeft,
    'info': iconInfo,
    'user': iconUser,
    'calendar': iconCalendar,
    'bulb': iconBulb,
})

const SolutionForm = () => {
    const { id } = useParams()
    const navigate = useNavigate()
    const { user } = useUser()

    const [problem, setProblem] = useState(null)
    const [loading, setLoading] = useState(true)

    const [description, setDescription] = useState('')
    const [submitting, setSubmitting] = useState(false)
    const [errorMsg, setErrorMsg] = useState('')

    useEffect(() => {
        if (!id) {
            setLoading(false)
            return
        }

        const fetchData = async () => {
            try {
                setLoading(true)
                setErrorMsg('')
                const data = await apiFetch(`/8d/rootcauses/${id}`)
                setProblem(data.problem || data)
            }
            catch (error) {
                console.error(error)
                setErrorMsg(error?.message || 'Failed to load context.')
            }
            finally {
                setLoading(false)
            }
        }

        fetchData()
    }, [id])

    const handleSubmit = async (e) => {
        e.preventDefault()

        setErrorMsg('')

        const trimmed = description.trim()
        if (!trimmed) {
            setErrorMsg('Description is required.')
            return
        }

        if (!user?.userId) {
            setErrorMsg('User not found.')
            return
        }

        const problemId = Number(problem?.problemId ?? problem?.id)
        if (!problemId) {
            setErrorMsg('Problem id not found.')
            return
        }

        try {
            setSubmitting(true)

            await apiFetch('/8d/solutions', {
                method: 'POST',
                body: JSON.stringify({
                    root_cause_id: Number(id),
                    author_id: Number(user.userId),
                    description: trimmed,
                    problem_id: Number(problem.id)
                }),
            })

            navigate(`/8d/problems/${problemId}/solutions`)
        }
        catch (error) {
            console.error(error)
            const status = error?.status ? ` (HTTP ${error.status})` : ''
            setErrorMsg(`${error?.message || 'Submit failed.'}${status}`)
        }
        finally {
            setSubmitting(false)
        }
    }

    if (loading) {
        return (
            <div style={{ height: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <IxSpinner variant="primary" size="large" />
            </div>
        )
    }

    if (!id || !problem) {
        return (
            <div style={{ padding: '2rem', textAlign: 'center' }}>
                <IxTypography format="h4">Problem context not found.</IxTypography>
                <IxButton onClick={() => navigate(-1)} variant="secondary">Go Back</IxButton>
            </div>
        )
    }

    return (
        <div style={{ padding: '2rem', width: '100%', boxSizing: 'border-box' }}>
            <div style={{ maxWidth: '1400px', margin: '0 auto' }}>
                <div style={{ marginBottom: '2rem', display: 'flex', alignItems: 'center', gap: '1rem' }}>
                    <IxButton icon="chevron-left" variant="secondary" outline onClick={() => navigate(-1)} disabled={submitting} />
                    <div>
                        <IxTypography format="label" color="soft">8D Process / Root Cause Analysis</IxTypography>
                        <IxTypography format="h3" style={{ margin: 0 }}>Define Solution</IxTypography>
                    </div>
                </div>

                {errorMsg && (
                    <div style={{ marginBottom: '1rem' }}>
                        <IxTypography format="body" style={{ color: 'var(--theme-color-alarm)' }}>
                            {errorMsg}
                        </IxTypography>
                    </div>
                )}

                <IxLayoutGrid>
                    <IxRow>
                        <IxCol size="12" sizeMd="3" style={{ marginBottom: '1.5rem' }}>
                            <IxCard variant="neutral" style={{ height: '100%', width: '100%' }}>
                                <IxCardContent style={{ padding: '2rem' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '1rem', color: 'var(--theme-color-primary)' }}>
                                        <IxIcon name="info" size="24" />
                                        <IxTypography format="h4" bold style={{ margin: 0 }}>Context Details</IxTypography>
                                    </div>

                                    <IxTypography format="body" color="std">
                                        {problem.description}
                                    </IxTypography>

                                    <IxDivider style={{ margin: '1.5rem 0' }} />

                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                                            <IxIcon name="user" size="16" color="color-soft-text" />
                                            <IxTypography format="label" color="soft">Author:</IxTypography>
                                            <IxTypography format="label" bold>{problem.author?.name || 'Unknown'}</IxTypography>
                                        </div>

                                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                                            <IxIcon name="calendar" size="16" color="color-soft-text" />
                                            <IxTypography format="label" color="soft">Created:</IxTypography>
                                            <IxTypography format="label" bold>{problem.createdAt}</IxTypography>
                                        </div>
                                    </div>
                                </IxCardContent>
                            </IxCard>
                        </IxCol>

                        <IxCol size="12" sizeMd="9">
                            <IxCard style={{ width: '100%' }}>
                                <IxCardContent style={{ padding: '2rem', width: '100%'  }}>
                                    <IxCardContent style={{ padding: '2rem', width: '100%' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '1.5rem' }}>
                                            <IxIcon name="bulb" size="24" color="color-primary" />
                                            <IxTypography format="h4" bold style={{ margin: 0 }}>New Solution</IxTypography>
                                        </div>

                                        <form onSubmit={handleSubmit}>
                                            <div style={{ marginBottom: '1.5rem', width: '100%'}}>
                                                <IxTypography format="label" style={{ marginBottom: '0.5rem', display: 'block' }}>
                                                    Description
                                                </IxTypography>

                                                <textarea
                                                    style={{
                                                        display: 'block',
                                                        width: '100%',
                                                        minHeight: '180px',
                                                        resize: 'none',
                                                        fontSize: '16px',
                                                        lineHeight: '1.5',
                                                        padding: '16px',
                                                        boxSizing: 'border-box',
                                                        border: '1px solid rgba(0, 0, 0, 0.2)',
                                                        borderRadius: '4px',
                                                        fontFamily: 'inherit',
                                                        backgroundColor: 'rgba(255, 255, 255, 0.05)',
                                                        color: 'inherit',
                                                        outline: 'none',
                                                        overflow: 'hidden'
                                                    }}
                                                    value={description}
                                                    onChange={(e) => {
                                                        setDescription(e.target.value);
                                                        e.target.style.height = 'auto';
                                                        e.target.style.height = `${e.target.scrollHeight}px`;
                                                    }}
                                                    maxLength={5000}
                                                    placeholder="Describe the proposed solution in detail..."
                                                    disabled={submitting}
                                                />
                                            </div>

                                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem' }}>
                                                <IxButton variant="secondary" outline onClick={() => navigate(-1)} disabled={submitting}>
                                                    Cancel
                                                </IxButton>

                                                <IxButton type="submit" disabled={submitting || !description.trim()}>
                                                    {submitting ? 'Submitting...' : 'Submit Solution'}
                                                </IxButton>
                                            </div>
                                        </form>
                                    </IxCardContent>
                                </IxCardContent>
                            </IxCard>
                        </IxCol>
                    </IxRow>
                </IxLayoutGrid>
            </div>
        </div>
    )
}

export default SolutionForm
