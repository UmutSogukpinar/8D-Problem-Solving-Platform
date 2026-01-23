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

    useEffect(() => {
        if (!id) {
            setLoading(false)
            return
        }

        const fetchData = async () => {
            try {
                setLoading(true)
                const data = await apiFetch(`/8d/rootcauses/${id}`)
                setProblem(data.problem || data)
            }
            catch (error) {
                console.error(error)
            }
            finally {
                setLoading(false)
            }
        }

        fetchData()
    }, [id])

    const handleSubmit = async (e) => {
        e.preventDefault()

        try {
            await apiFetch('/8d/solutions', {
                method: 'POST',
                body: JSON.stringify({
                    root_cause_id: Number(id),
                    author_id: user?.userId,
                    description,
                }),
            })

            navigate(-1)
        }
        catch (error) {
            console.error(error)
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
        <div
            style={{
                padding: '2rem',
                width: '100%',
                boxSizing: 'border-box',
            }}
        >
            <div
                style={{
                    maxWidth: '1400px',
                    margin: '0 auto',
                }}
            >
                <div style={{ marginBottom: '2rem', display: 'flex', alignItems: 'center', gap: '1rem' }}>
                    <IxButton icon="chevron-left" variant="secondary" outline onClick={() => navigate(-1)} />
                    <div>
                        <IxTypography format="label" color="soft">8D Process / Root Cause Analysis</IxTypography>
                        <IxTypography format="h3" style={{ margin: 0 }}>Define Solution</IxTypography>
                    </div>
                </div>

                <IxLayoutGrid>
                    <IxRow>
                        {/* Left card smaller on desktop */}
                        <IxCol size="12" sizeMd="3" style={{ marginBottom: '1.5rem' }}>
                            <IxCard variant="neutral" style={{ height: '100%', width: '100%' }}>
                                <IxCardContent style={{ padding: '2rem' }}>
                                    <div
                                        style={{
                                            display: 'flex',
                                            alignItems: 'center',
                                            gap: '0.5rem',
                                            marginBottom: '1rem',
                                            color: 'var(--theme-color-primary)',
                                        }}
                                    >
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

                        {/* Right card bigger on desktop */}
                        <IxCol size="12" sizeMd="9">
                            <IxCard style={{ width: '100%' }}>
                                <IxCardContent style={{ padding: '2rem' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '1.5rem' }}>
                                        <IxIcon name="bulb" size="24" color="color-primary" />
                                        <IxTypography format="h4" bold style={{ margin: 0 }}>New Solution</IxTypography>
                                    </div>

                                    <form onSubmit={handleSubmit}>
                                        <div style={{ marginBottom: '1.5rem' }}>
                                            <IxTypography format="label" style={{ marginBottom: '0.5rem', display: 'block' }}>
                                                Description
                                            </IxTypography>

                                            <IxTextarea
                                                style={{
                                                    width: '100%',
                                                    minHeight: '380px',
                                                    maxHeight: '380px',
                                                    resize: 'none',
                                                    fontSize: '16px',
                                                    lineHeight: '1.7',
                                                    boxSizing: 'border-box',
                                                }}
                                                value={description}
                                                onInput={(e) => setDescription(e.target.value)}
                                                maxLength={5000}
                                                placeholder="Describe the proposed solution in detail..."
                                            />
                                        </div>

                                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem' }}>
                                            <IxButton variant="secondary" outline onClick={() => navigate(-1)}>
                                                Cancel
                                            </IxButton>

                                            <IxButton type="submit" disabled={!description.trim()}>
                                                Submit Solution
                                            </IxButton>
                                        </div>
                                    </form>
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
