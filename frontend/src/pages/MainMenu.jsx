import React from "react";
import {
    IxCard,
    IxCardContent,
    IxDivider,
    IxLayoutGrid,
    IxRow,
    IxCol,
    IxTypography,
} from "@siemens/ix-react";

export default function MainMenu()
{
    return (
        <div style={{ padding: 24, maxWidth: 1100, margin: "0 auto" }}>
            <IxTypography variant="h1">
                8D Problem Solving Platform (MVP)
            </IxTypography>

            <IxTypography
                variant="body-lg"
                style={{ opacity: 0.85, marginTop: 8 }}
            >
                A full-stack prototype demonstrating
                <strong> Problem Definition (D1–D2)</strong> and
                <strong> Root Cause Analysis & Solution (D4–D5)</strong>
                using the Siemens iX Design System.
            </IxTypography>

            <IxDivider style={{ margin: "20px 0" }} />

            <IxLayoutGrid>
                <IxRow>
                    <IxCol size="12">
                        <IxCard variant="outline">
                            <IxCardContent>
                                <IxTypography variant="h3">
                                    Scenario Overview
                                </IxTypography>

                                <IxTypography
                                    variant="body"
                                    style={{ marginTop: 10, lineHeight: 1.7 }}
                                >
                                    This application digitalizes the <strong>8D problem-solving
                                    methodology</strong> used in manufacturing environments.
                                    Users analyze problems by repeatedly asking <em>“Why?”</em>,
                                    building a hierarchical cause tree, and identifying the
                                    confirmed root cause.
                                </IxTypography>
                            </IxCardContent>
                        </IxCard>
                    </IxCol>
                </IxRow>

                <IxRow>
                    <IxCol size="12" md="6">
                        <IxCard variant="outline" style={{ height: "100%" }}>
                            <IxCardContent>
                                <IxTypography variant="h3">
                                    Application Sections
                                </IxTypography>

                                <IxTypography
                                    variant="h4"
                                    style={{ marginTop: 12 }}
                                >
                                    Section A — Dashboard (D1–D2)
                                </IxTypography>

                                <IxTypography
                                    variant="body"
                                    style={{ lineHeight: 1.7 }}
                                >
                                    A table lists existing problems with columns such as
                                    ID, Title, Responsible Crew, Status, and Created Date.
                                    New problems are created via a modal dialog.
                                </IxTypography>

                                <IxDivider style={{ margin: "12px 0" }} />

                                <IxTypography variant="h4">
                                    Section B — Root Cause & Solution (D4–D5)
                                </IxTypography>

                                <IxTypography
                                    variant="body"
                                    style={{ lineHeight: 1.7 }}
                                >
                                    Each problem contains a recursive root cause tree.
                                    Causes can have unlimited child causes.
                                    Any node can be marked as the <strong>Root Cause</strong>,
                                    enabling entry of a permanent corrective action.
                                </IxTypography>
                            </IxCardContent>
                        </IxCard>
                    </IxCol>

                    <IxCol size="12" md="6">
                        <IxCard variant="outline" style={{ height: "100%" }}>
                            <IxCardContent>
                                <IxTypography variant="h3">
                                    Technology Stack
                                </IxTypography>

                                <ul style={{ marginTop: 12, paddingLeft: 18 }}>
                                    <li>
                                        <IxTypography variant="body">
                                            <strong>Frontend:</strong> React + Siemens iX Design System
                                        </IxTypography>
                                    </li>
                                    <li>
                                        <IxTypography variant="body">
                                            <strong>Backend:</strong> Native PHP with RESTful JSON API
                                        </IxTypography>
                                    </li>
                                    <li>
                                        <IxTypography variant="body">
                                            <strong>Database:</strong> MySQL (or any relational database)
                                        </IxTypography>
                                    </li>
                                </ul>
                            </IxCardContent>
                        </IxCard>
                    </IxCol>
                </IxRow>

                <IxRow>
                    <IxCol size="12">
                        <IxCard variant="outline">
                            <IxCardContent>
                                <IxTypography variant="h3">
                                    What This MVP Demonstrates
                                </IxTypography>

                                <ul style={{ marginTop: 12, paddingLeft: 18 }}>
                                    <li>
                                        <IxTypography variant="body">
                                            Modeling recursive tree structures using
                                            relational data (<strong>parent_id</strong>)
                                        </IxTypography>
                                    </li>
                                    <li>
                                        <IxTypography variant="body">
                                            Designing a clean REST API that returns
                                            tree-friendly JSON
                                        </IxTypography>
                                    </li>
                                    <li>
                                        <IxTypography variant="body">
                                            Correct usage of Siemens iX UI components
                                            (cards, typography, inputs, modals)
                                        </IxTypography>
                                    </li>
                                </ul>

                                <IxDivider style={{ margin: "12px 0" }} />

                                <IxTypography variant="body">
                                    Delivery includes a GitHub/GitLab repository
                                    and a README explaining how to run the project locally.
                                </IxTypography>
                            </IxCardContent>
                        </IxCard>
                    </IxCol>
                </IxRow>
            </IxLayoutGrid>
        </div>
    );
}
