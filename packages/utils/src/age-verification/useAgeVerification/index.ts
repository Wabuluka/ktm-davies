import { useCallback, useEffect, useState } from 'react'
import { AgeVerificationState } from '../AgeVerificationContext'

const key = 'ageVverification_lastVerifiedAt'

function restoreLastVerifiedAt() {
  const value = localStorage.getItem(key)
  if (!!value) {
    const lastVerifiedAt = Number(value)
    if (!isNaN(lastVerifiedAt)) {
      return lastVerifiedAt
    }
  }
  return null
}

function storeLastVerifiedAt(lastVerifiedA: number) {
  localStorage.setItem(key, lastVerifiedA.toString())
}

function isVerified({ expires }: Props) {
  const lastVerifiedAt = restoreLastVerifiedAt()
  if (lastVerifiedAt === null) {
    return false
  }

  return (
    Date.now() - lastVerifiedAt <
    expires.day * 24 * 60 * 60 * 1000 + expires.hour * 60 * 60 * 1000
  )
}

type Props = {
  expires: {
    day: number
    hour: number
  }
}
export function useAgeVerification({ expires }: Props) {
  // NOTE: リロード時に一瞬ダイアログが表示されるのを防ぐため、初期値を true にしておく
  const [{ verified }, setState] = useState<AgeVerificationState>({
    verified: true,
  })
  useEffect(() => {
    if (!isVerified({ expires })) {
      setState({ verified: false })
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])
  const onVerify = useCallback(() => {
    storeLastVerifiedAt(Date.now())
    setState({ verified: true })
  }, [])

  return {
    verified,
    onVerify,
  }
}
