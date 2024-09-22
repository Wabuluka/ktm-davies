import { PropsWithChildren, createContext, useContext, useMemo } from 'react';
import { Creator } from '../Types';

type Value = {
  onStoreSuccess?: (creator: Creator) => void;
  onDeleteSuccess?: (creatorId: string) => void;
  onUpdateSuccess?: (creator: Creator) => void;
};

const CreatorEventListenerContext = createContext<Value>({});

export function useCreatorEventListener() {
  return useContext(CreatorEventListenerContext);
}

export function CreatorEventListenerProvider({
  onStoreSuccess,
  onDeleteSuccess,
  onUpdateSuccess,
  children,
}: PropsWithChildren<Value>) {
  const paremtListeners = useCreatorEventListener();
  const value = useMemo(
    () => ({
      onStoreSuccess: (creator: Creator) => {
        onStoreSuccess?.(creator);
        paremtListeners.onStoreSuccess?.(creator);
      },
      onDeleteSuccess: (creatorId: string) => {
        onDeleteSuccess?.(creatorId);
        paremtListeners.onDeleteSuccess?.(creatorId);
      },
      onUpdateSuccess: (creator: Creator) => {
        onUpdateSuccess?.(creator);
        paremtListeners.onUpdateSuccess?.(creator);
      },
    }),
    [onDeleteSuccess, onStoreSuccess, onUpdateSuccess, paremtListeners],
  );

  return (
    <CreatorEventListenerContext.Provider value={value}>
      {children}
    </CreatorEventListenerContext.Provider>
  );
}
