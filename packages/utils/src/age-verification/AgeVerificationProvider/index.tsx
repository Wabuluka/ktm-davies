'use client'

import { PropsWithChildren } from 'react'
import { useAgeVerification } from '../useAgeVerification'
import {
  AgeVerificationStateContext,
  AgeVerificationActionContext,
} from '../AgeVerificationContext'

type Props = PropsWithChildren<{
  expires: {
    day: number
    hour: number
  }
}>

export function AgeVerifycationProvider({ expires, children }: Props) {
  const { verified, onVerify: verify } = useAgeVerification({ expires })
  return (
    <AgeVerificationStateContext.Provider value={{ verified }}>
      <AgeVerificationActionContext.Provider value={{ verify }}>
        {children}
      </AgeVerificationActionContext.Provider>
    </AgeVerificationStateContext.Provider>
  )
}
