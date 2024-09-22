import { PropsWithChildren, createContext, useContext, useMemo } from 'react';
import { CreationType } from '../Types';

type Value = {
  onStoreSuccess?: (creationType: CreationType) => void;
  onDeleteSuccess?: (creationTypeName: string) => void;
  onUpdateSuccess?: (creationType: CreationType, prevName: string) => void;
  onOrderUpSuccess?: (creationType: CreationType) => void;
  onOrderDownSuccess?: (creationType: CreationType) => void;
};

const CreationTypeListenerContext = createContext<Value>({});

export function useCreationTypeEventListener() {
  return useContext(CreationTypeListenerContext);
}

export function CreationTypeEventListenerProvider({
  onStoreSuccess,
  onDeleteSuccess,
  onUpdateSuccess,
  onOrderUpSuccess,
  onOrderDownSuccess,
  children,
}: PropsWithChildren<Value>) {
  const paremtListeners = useCreationTypeEventListener();
  const value = useMemo(
    () => ({
      onStoreSuccess: (creationType: CreationType) => {
        onStoreSuccess?.(creationType);
        paremtListeners.onStoreSuccess?.(creationType);
      },
      onDeleteSuccess: (creationTypeName: string) => {
        onDeleteSuccess?.(creationTypeName);
        paremtListeners.onDeleteSuccess?.(creationTypeName);
      },
      onUpdateSuccess: (creationType: CreationType, prevName: string) => {
        onUpdateSuccess?.(creationType, prevName);
        paremtListeners.onUpdateSuccess?.(creationType, prevName);
      },
      onOrderUpSuccess: (creationType: CreationType) => {
        onOrderUpSuccess?.(creationType);
        paremtListeners.onOrderUpSuccess?.(creationType);
      },
      onOrderDownSuccess: (creationType: CreationType) => {
        onOrderDownSuccess?.(creationType);
        paremtListeners.onOrderDownSuccess?.(creationType);
      },
    }),
    [
      onDeleteSuccess,
      onOrderDownSuccess,
      onOrderUpSuccess,
      onStoreSuccess,
      onUpdateSuccess,
      paremtListeners,
    ],
  );

  return (
    <CreationTypeListenerContext.Provider value={value}>
      {children}
    </CreationTypeListenerContext.Provider>
  );
}
