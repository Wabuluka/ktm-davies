'use client'

import { createContext, useContext } from 'react'

export type AgeVerificationState = {
  verified: boolean
}

export type AgeVerificationAction = {
  verify: () => void
}

export const AgeVerificationStateContext = createContext<AgeVerificationState>({
  verified: false,
})

export const AgeVerificationActionContext =
  createContext<AgeVerificationAction>({
    verify: () => {},
  })

export function useAgeVerificationState() {
  return useContext(AgeVerificationStateContext)
}

export function useAgeVerificationAction() {
  return useContext(AgeVerificationActionContext)
}
